/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package junti;

import beans.Entry;
import beans.FilteredEntry;
import beans.Ride;
import com.google.gson.Gson;
import com.mongodb.*;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.logging.Level;
import java.util.logging.Logger;
import junti.DBConnection.MysqlDB;
import junti.DBConnection.NamedParameterStatement;
import junti.DBConnection.PostgresqlDB;

/**
 *
 * @author steph
 */
public class runner implements Runnable {

    private static Connection conn = null;


    @Override
    public void run() {
        ResultSet rset = null;
        Statement s = null;
        Gson gson = new Gson();
        NamedParameterStatement sql_named_state = null;
        NamedParameterStatement sql_p_main = null;
        NamedParameterStatement sql_p_user = null;
        Integer UserId;
        String sql;
        String sqlmain;
        try {
            long server_time = 0;
            Integer Road_info = 0;
            Entry temp_entry;
            ArrayList<Entry> UnfilteredEntry = new ArrayList<Entry>();
            ArrayList<FilteredEntry> UpdatedEntry = new ArrayList<FilteredEntry>();
            ArrayList<Integer> UserList = new ArrayList<Integer>();
            Ride UserRide;//Одна поездка пользователя
            Integer UnfilteredCount=0;
            int i=0;
            conn = MysqlDB.getConnection();
            System.out.println("Start");
            //1 Для начала получаем все данные по дорогам
            //Выбираем всех пользователей для начала
            sqlmain = "Select distinct(UID) as UserID from NTIEntry order by UserID;";
            s = conn.createStatement();
            rset = s.executeQuery(sqlmain);
            while (rset.next()) 
                {
                    //Если пользователь был авторизован, то обрабатываем его данные 
                    if(rset.getInt("UserID")!=-3)
                        UserList.add(rset.getInt("UserID"));
                }
            //Отлично, получили пользователей
            //Теперь начинаем получать поездки пользователя, которые
           for (Integer UID : UserList)
            {
                    sqlmain = "Select * from NTIEntry where UID=:UID and Deleted=0 order by utimestamp;";
                    sql_p_main = new NamedParameterStatement(conn, sqlmain);
                    sql_p_main.setInt("UID", UID); 
                    rset=sql_p_main.executeQuery();
                    //Начинаем получать все точки
                    while (rset.next()) 
                    {
                        temp_entry= new Entry();
                        temp_entry.setAccx(rset.getDouble("accx"));
                        temp_entry.setAccy(rset.getDouble("accy"));
                        temp_entry.setCompass(rset.getDouble("compass"));
                        temp_entry.setDirection(rset.getDouble("direction"));
                        temp_entry.setDistance(rset.getDouble("distance"));
                        temp_entry.setLat(rset.getDouble("lat"));
                        temp_entry.setLng(rset.getDouble("lng"));
                        temp_entry.setSpeed(rset.getDouble("speed"));
                        temp_entry.setTimestamp(rset.getDouble("utimestamp"));
                        temp_entry.setUID(rset.getInt("UID"));
                        temp_entry.setId(rset.getInt("id"));  
                        UnfilteredEntry.add(temp_entry);                               
                    }
                    //Отлично, мы добавилии эти вот точки, теперь начинаем их обрабатывать 
                    //Для начала перегоним это в массив, тк так будет егче работать 
                    UnfilteredCount=UnfilteredEntry.size();
                    Entry[] ArrayEntry=new Entry[UnfilteredEntry.size()];
                    UnfilteredEntry.toArray(ArrayEntry);
                    //Теперь очищаем его 
                    UnfilteredEntry.clear();
                    for(i=1;i<UnfilteredCount;i++)
                    {
                        //Начинаем разбивание точек на пути
                        /*
                         * Точка является частью пути 
                         * 1)Скорость между 2-мя точками не должна быть более 200 км/ч
                         * 2)Время  между ними не должно превышать 5 мин
                         */
                        if((ArrayEntry[i].getTimestamp()-ArrayEntry[i-1].getTimestamp())<300)
                        {
                            if(DistanceBetweenPoints(ArrayEntry[i].getLat(),ArrayEntry[i-1].getLat(),ArrayEntry[i].getLng(),ArrayEntry[i-1].getLng())/((ArrayEntry[i].getTimestamp()-ArrayEntry[i-1].getTimestamp()))<200)
                            {
                            
                            }
                        }
                    }
            }
        
            
         

        }
        catch (ClassNotFoundException ex) {
            Logger.getLogger(runner.class.getName()).log(Level.SEVERE, null, ex);
        } catch (SQLException ex) {
            Logger.getLogger(runner.class.getName()).log(Level.SEVERE, null, ex);
        }        
    }
          
    
    private static double  DistanceBetweenPoints( Double lat1,Double lat2,Double lng1,Double lng2 ) 
    {
  
        return Math.acos(Math.sin(lat1)*Math.sin(lat2)+Math.cos(lat1)*Math.cos(lat2)*Math.cos(lng2)-lng1)*111.2;
        
    }
}
