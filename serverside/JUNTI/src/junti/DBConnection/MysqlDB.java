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

    private static final String myUrl = "jdbc:mysql://localhost:3306/NTI";
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


    public static void cleanUp(Connection conn) {
        try {
            conn.close();
        } catch (SQLException ex) {
            Logger.getLogger(MysqlDB.class.getName()).log(Level.SEVERE, null, ex);
        }

    }
}
