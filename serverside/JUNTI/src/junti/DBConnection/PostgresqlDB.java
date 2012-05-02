/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package junti.DBConnection;

import java.sql.*;
import java.util.logging.Level;
import java.util.logging.Logger;

/**
 *
 * @author steph
 */
public class PostgresqlDB {

    public static Connection getConnection() throws ClassNotFoundException {
        Class.forName("org.postgresql.Driver");
        Connection conn = null;
        try {
            conn = DriverManager.getConnection("jdbc:postgresql://127.0.0.1/world", "root", "");
        } catch (SQLException ex) {
            System.out.println(ex.getErrorCode());
            System.out.println(ex.getMessage());

            Logger.getLogger(PostgresqlDB.class.getName()).log(Level.SEVERE, null, ex);
        }
        return conn;
    }
    
    public static void cleanUp(Connection conn) {
        closeConnection(conn);
    }
    
    public static void cleanUp(Connection conn, NamedParameterStatement nps) {
        closeNps(nps);
        closeConnection(conn);
    }
    
    public static void cleanUp(Connection conn, NamedParameterStatement nps, ResultSet rset) {
        
    }
    
    private static void closeNps(NamedParameterStatement nps) {
        try {
            if(nps != null) {
                nps.close();
            } 
        } catch(SQLException ex) {
        }
    } 

    private static void closeConnection(Connection conn) {
        try {
            if (conn != null) {
                conn.close();
            }
        } catch (SQLException ex) {
        }
    }
    
    private static void closeResultSet(ResultSet rset) {
        try {
            if(rset != null) {
                rset.close();
            }
        } catch(SQLException ex) {
            
        }
    }
}
