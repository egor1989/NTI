/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package junti.DBConnection;

import java.sql.*;
import java.util.Properties;
import java.util.logging.Level;
import java.util.logging.Logger;

/**
 *
 * @author steph
 */
public class MysqlDB {

    private static final String myUrl = "jdbc:mysql://localhost:3306/goodroads";
    private static final String BackUpUrl = "jdbc:mysql://localhost:3306/Area";
    
private static final String GoodroadsMain = "jdbc:mysql://localhost:3306/GoodroadsMain";
private static final String GoodroadsArea = "jdbc:mysql://localhost:3306/GoodroadsArea";
private static final String GoodroadsUsers = "jdbc:mysql://localhost:3306/GoodroadsUsers";

    public static Connection getConnection() throws ClassNotFoundException, SQLException {
        String myDriver = "com.mysql.Driver";
        try {
            Class.forName(myDriver);
        } catch (ClassNotFoundException ex) {
        }
        Properties properties = new Properties();
        properties.setProperty("user", "steph");
        properties.setProperty("password", "trinitro");
        properties.setProperty("characterEncoding","UTF-8");
        Connection conn = DriverManager.getConnection(myUrl, properties);
        return conn;
    }
public static Connection getConnectionArea() throws ClassNotFoundException, SQLException {
        String myDriver = "com.mysql.Driver";
        try {
            Class.forName(myDriver);
        } catch (ClassNotFoundException ex) {
        }
        Properties properties = new Properties();
        properties.setProperty("user", "steph");
        properties.setProperty("password", "trinitro");
          properties.setProperty("characterEncoding","UTF-8");
        Connection conn = DriverManager.getConnection(GoodroadsArea, properties);
        return conn;
    }
public static Connection getConnectionMain() throws ClassNotFoundException, SQLException {
        String myDriver = "com.mysql.Driver";
        try {
            Class.forName(myDriver);
        } catch (ClassNotFoundException ex) {
        }
        Properties properties = new Properties();
        properties.setProperty("user", "steph");
        properties.setProperty("password", "trinitro");
          properties.setProperty("characterEncoding","UTF-8");
        Connection conn = DriverManager.getConnection(GoodroadsMain, properties);
        return conn;
    }


public static Connection getConnectionUsers() throws ClassNotFoundException, SQLException {
        String myDriver = "com.mysql.Driver";
        try {
            Class.forName(myDriver);
        } catch (ClassNotFoundException ex) {
        }
        Properties properties = new Properties();
        properties.setProperty("user", "steph");
        properties.setProperty("password", "trinitro");
          properties.setProperty("characterEncoding","UTF-8");
        Connection conn = DriverManager.getConnection(GoodroadsUsers, properties);
        return conn;
    }



    public static void cleanUp(Connection conn) {
        try {
            conn.close();
        } catch (SQLException ex) {
            Logger.getLogger(MysqlDB.class.getName()).log(Level.SEVERE, null, ex);
        }

    }
}
