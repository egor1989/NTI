/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package junti.DBConnection;

import com.mongodb.*;
import java.net.UnknownHostException;
import java.util.logging.Level;
import java.util.logging.Logger;

/**
 *
 * @author steph
 */
public class MongoDB {
           public static Mongo getConnection() throws ClassNotFoundException{
         Mongo connection=null;
          
               try {
             connection = new Mongo();
          //    db = connection.getDB(Database);
        } catch (UnknownHostException ex) {
            Logger.getLogger(MongoDB.class.getName()).log(Level.SEVERE, null, ex);
        } catch (MongoException ex) {
            Logger.getLogger(MongoDB.class.getName()).log(Level.SEVERE, null, ex);
        }
               return connection;
    }
  public static void cleanUp(Mongo conn) {
        conn.close();
      
    }
}
