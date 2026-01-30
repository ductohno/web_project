package com.example.sqli.controller;

import com.example.sqli.model.User;
import com.example.sqli.util.DBFactory;
import com.example.sqli.config.DbInitializer;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.*;

import java.sql.*;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

@Controller
public class WebController {

    private final DBFactory dbFactory;
    private final DbInitializer dbInitializer;

    public WebController(DBFactory dbFactory, DbInitializer dbInitializer) {
        this.dbFactory = dbFactory;
        this.dbInitializer = dbInitializer;
    }

    @GetMapping("/")
    public String index(Model model) {
        List<User> users = new ArrayList<>();
        try (Connection conn = dbFactory.getConnection();
                Statement stmt = conn.createStatement();
                ResultSet rs = stmt.executeQuery("SELECT * FROM users")) {

            while (rs.next()) {
                users.add(new User(rs.getInt("id"), rs.getString("name"), rs.getString("email")));
            }
        } catch (SQLException e) {
            model.addAttribute("error", "Error fetching users: " + e.getMessage());
        }
        model.addAttribute("users", users);
        model.addAttribute("dbType", dbFactory.getDbType());
        return "index";
    }

    @PostMapping("/add")
    public String addUser(@RequestParam String name, @RequestParam String email, Model model) {
        // Safe PreparedStatement
        String sql = "INSERT INTO users (name, email) VALUES (?, ?)";
        try (Connection conn = dbFactory.getConnection();
                PreparedStatement pstmt = conn.prepareStatement(sql)) {
            pstmt.setString(1, name);
            pstmt.setString(2, email);
            pstmt.executeUpdate();
        } catch (SQLException e) {
            model.addAttribute("error", "Error adding user: " + e.getMessage());
            return index(model);
        }
        return "redirect:/";
    }

    @PostMapping("/update")
    public String updateUser(@RequestParam int id, @RequestParam String name, @RequestParam String email, Model model) {
        // Safe PreparedStatement
        String sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
        try (Connection conn = dbFactory.getConnection();
                PreparedStatement pstmt = conn.prepareStatement(sql)) {
            pstmt.setString(1, name);
            pstmt.setString(2, email);
            pstmt.setInt(3, id);
            pstmt.executeUpdate();
        } catch (SQLException e) {
            model.addAttribute("error", "Error updating user: " + e.getMessage());
            return index(model);
        }
        return "redirect:/";
    }

    @PostMapping("/delete")
    public String deleteUser(@RequestParam int id, Model model) {
        // Safe PreparedStatement
        String sql = "DELETE FROM users WHERE id = ?";
        try (Connection conn = dbFactory.getConnection();
                PreparedStatement pstmt = conn.prepareStatement(sql)) {
            pstmt.setInt(1, id);
            pstmt.executeUpdate();
        } catch (SQLException e) {
            model.addAttribute("error", "Error deleting user: " + e.getMessage());
            return index(model);
        }
        return "redirect:/";
    }

    @GetMapping("/search")
    public String search(@RequestParam String query, Model model) {
        List<User> users = new ArrayList<>();
        // VULNERABLE SQL INJECTION
        // Using string concatenation directly into the query
        String sql = "SELECT * FROM users WHERE name LIKE '%" + query + "%'";

        try (Connection conn = dbFactory.getConnection();
                Statement stmt = conn.createStatement();
                ResultSet rs = stmt.executeQuery(sql)) {

            while (rs.next()) {
                users.add(new User(rs.getInt("id"), rs.getString("name"), rs.getString("email")));
            }
        } catch (SQLException e) {
            model.addAttribute("error", "SQL Error: " + e.getMessage());
        }

        model.addAttribute("users", users);
        model.addAttribute("searchQuery", query);
        model.addAttribute("executedSql", sql); // Show the executed SQL for educational purposes
        model.addAttribute("dbType", dbFactory.getDbType());
        return "index";
    }

    // ============ Database Management API Endpoints ============

    /**
     * Get list of available databases
     */
    @GetMapping("/api/databases")
    @ResponseBody
    public Map<String, Object> getAvailableDatabases() {
        Map<String, Object> response = new HashMap<>();
        List<String> databases = dbFactory.getAvailableDatabases();

        // Test connectivity for each database
        Map<String, Boolean> connectivity = new HashMap<>();
        for (String db : databases) {
            connectivity.put(db, dbFactory.testConnection(db));
        }

        response.put("databases", databases);
        response.put("connectivity", connectivity);
        response.put("current", dbFactory.getDbType());

        return response;
    }

    /**
     * Get current database information
     */
    @GetMapping("/api/current-database")
    @ResponseBody
    public Map<String, Object> getCurrentDatabase() {
        Map<String, Object> response = new HashMap<>();
        response.put("type", dbFactory.getDbType());
        response.put("connectionInfo", dbFactory.getConnectionInfo());
        response.put("connected", dbFactory.testConnection(dbFactory.getDbType()));

        return response;
    }

    /**
     * Switch to a different database
     */
    @PostMapping("/api/switch-database")
    @ResponseBody
    public Map<String, Object> switchDatabase(@RequestParam String dbType) {
        Map<String, Object> response = new HashMap<>();

        try {
            String oldDb = dbFactory.getDbType();

            // Switch the database
            dbFactory.switchDatabase(dbType);

            // Initialize the new database (create tables if needed)
            dbInitializer.initializeDatabase();

            response.put("success", true);
            response.put("message", "Successfully switched from " + oldDb + " to " + dbType);
            response.put("oldDatabase", oldDb);
            response.put("newDatabase", dbType);

        } catch (Exception e) {
            response.put("success", false);
            response.put("message", "Failed to switch database: " + e.getMessage());
            response.put("error", e.getClass().getSimpleName());
        }

        return response;
    }
}
