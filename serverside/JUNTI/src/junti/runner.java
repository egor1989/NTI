/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package junti;

import beans.Entry;
import beans.FilteredEntry;
import beans.Ride;
import com.google.gson.Gson;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.logging.Level;
import java.util.logging.Logger;
import junti.DBConnection.MysqlDB;
import junti.DBConnection.NamedParameterStatement;

/**
 *
 * @author steph
 */
public class runner implements Runnable {

    private static Connection conn = null;  
    
    @Override
    public void run() {
        try {
            ResultSet rset = null;
            Statement s = null;
            Gson gson = new Gson();
            NamedParameterStatement sql_named_state = null;
            NamedParameterStatement sql_p_main = null;
            NamedParameterStatement sql_p_user = null;
            Integer UserId;
            String sql;
            String sqlmain;
            System.out.println("Started new transaction");
            
                long server_time = 0;
                Integer Road_info = 0;
                Entry temp_entry;
                Ride temp_ride;
                int UserTempRoadId=0;
                ArrayList<FilteredEntry> UnfilteredEntry = new ArrayList<FilteredEntry>();
                ArrayList<FilteredEntry> UpdatedEntry = new ArrayList<FilteredEntry>();
                ArrayList<Integer> UserList = new ArrayList<Integer>();
                ArrayList<Ride> UserRide=new ArrayList<Ride>();;//Одна поездка пользователя
                Integer UnfilteredCount=0;
                int i=0;
                conn = MysqlDB.getConnection();
                //1 Для начала получаем все данные по дорогам
                //Выбираем всех пользователей для начала
                sqlmain = "Select distinct(UID) as UserID from NTIEntry where UID>0 order by UserID;";
                s = conn.createStatement();
                rset = s.executeQuery(sqlmain);
                while (rset.next()) 
                    {
                        //Если пользователь был авторизован, то обрабатываем его данные 
                       
                            UserList.add(rset.getInt("UserID"));
                    }
                //Отлично, получили пользователей
                //Теперь начинаем получать поездки пользователя, которые
               for (Integer UID : UserList)
                {
                        sqlmain = "Select * from NTIEntry where UID=:UID and Deleted=0 AND Speed>0 AND lat != 0 AND lng != 0 group by utimestamp order by utimestamp;";
                        sql_p_main = new NamedParameterStatement(conn, sqlmain);
                        sql_p_main.setInt("UID", UID); 
                        rset=sql_p_main.executeQuery();
                        //Начинаем получать все точки
                        UserRide.clear();
                        UnfilteredEntry.clear();
                        
                        while (rset.next()) 
                        {
                            UnfilteredEntry.add(new FilteredEntry(rset.getDouble("accx"),rset.getDouble("accy"),rset.getDouble("compass"),rset.getDouble("direction"),rset.getDouble("distance"),rset.getDouble("lat"),rset.getDouble("lng"),rset.getDouble("speed"),rset.getDouble("utimestamp"),rset.getInt("UID"),rset.getInt("id")));    
                        }
                        //Обнуляем все оставшиеся точки
                        sqlmain = "Update NTIEntry set Deleted=1 where UID=:UID";
                        sql_p_main = new NamedParameterStatement(conn, sqlmain);
                        sql_p_main.setInt("UID", UID); 
                        sql_p_main.execute();

                        //Отлично, мы добавилии эти вот точки, теперь начинаем их обрабатывать 
                        //Для начала перегоним это в массив, тк так будет егче работать 
                        FilteredEntry[] ArrayEntry=new FilteredEntry[UnfilteredEntry.size()];
                        UnfilteredEntry.toArray(ArrayEntry);
                        //Теперь очищаем его 
                       UnfilteredEntry.clear();
                    
                       
                        for(i=1;i<ArrayEntry.length;i++)
                        {
                            //Начинаем разбивание точек на пути
                            /*
                             * Точка является частью пути 
                             * 1)Скорость между 2-мя точками не должна быть более 200 км/ч
                             * 2)Время  между ними не должно превышать 5 мин
                            */
                            //TODO: Сделать нормальную разбивку по поездкам
                            if(UnfilteredEntry.size()>0)
                            {
                                if(( ArrayEntry[i-1].getTimestamp()-UnfilteredEntry.get(UnfilteredEntry.size()-1).getTimestamp())<300 && DistanceBetweenPoints(UnfilteredEntry.get(UnfilteredEntry.size()-1).getLat(),ArrayEntry[i-1].getLat(),  UnfilteredEntry.get(UnfilteredEntry.size()-1).getLng(),ArrayEntry[i-1].getLng())<0.2)
                                    {
                                     //   System.out.println((UnfilteredEntry.get(UnfilteredEntry.size()-1).getTimestamp()-ArrayEntry[i-1].getTimestamp())+"::::"+DistanceBetweenPoints(UnfilteredEntry.get(UnfilteredEntry.size()-1).getLat(),ArrayEntry[i-1].getLat(),  UnfilteredEntry.get(UnfilteredEntry.size()-1).getLng(),ArrayEntry[i-1].getLng()));
                                     UnfilteredEntry.add(ArrayEntry[i-1]);
                                 }
                            else
                            {

                                UserRide.add(new Ride());
                                UserRide.get(UserRide.size()-1).setFilteredEntryRide(new ArrayList<FilteredEntry>(UnfilteredEntry));
                                UnfilteredEntry.clear();
                            }
                            }
                            else
                            {
                                UnfilteredEntry.add(ArrayEntry[i-1]);
                            }
                          
                        }
                        //Теперь удаляем определенно херовые поезки
                        //Если в поездке 0 скоростей больше половины - эт какая-то хуита ребята

                        System.out.println(UID+"user rides after filtering "+UserRide.size());
                       //Теперь начинаем считать время начала и конца поездок
                        //Теперь добавляем проверку на то , что длинна поездки должна быть больше 50 точек
                        
                        for(i=0;i<UserRide.size();i++)
                        {
                            
                           UserRide.get(i).setTimeStart( UserRide.get(i).getFilteredEntryRide().get(0).getTimestamp());
                           UserRide.get(i).setTimeEnd( UserRide.get(i).getFilteredEntryRide().get(UserRide.get(i).getFilteredEntryRide().size()-1).getTimestamp());
                           System.out.println(UserRide.get(i).getFilteredEntryRide().size()+":::"+UserRide.get(i).getTimeStart()+":"+UserRide.get(i).getTimeEnd());
                            
                        }
                       
                       //отлично, выкинули все элементы , которые были не нужны
                        //Теперь по каждой поездке, если она не нулевая начинаем высчитывать все данные
                          for(int j=0;j<UserRide.size();j++)
                        {
                                 //Подсчет крутости торможения/ускорения, поворота и скорости точки.
                             if(UserRide.get(j).getFilteredEntryRide().size()>50)
                             {
                             
                                for(i=1;i< UserRide.get(j).getFilteredEntryRide().size()-1;i++) 
                                {
                                    UserRide.get(j).getFilteredEntryRide().get(i).setSevAcc(0);
                                    UserRide.get(j).getFilteredEntryRide().get(i).setSevTurn(0);
                                    UserRide.get(j).getFilteredEntryRide().get(i).setSevSpeed(0);
                                   UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("normal point");
                                   UserRide.get(j).getFilteredEntryRide().get(i).setTypeSpeed("normal point");
                                   UserRide.get(j).getFilteredEntryRide().get(i).setTypeTurn("normal point");
                                   
                                   
                                   double speed = UserRide.get(j).getFilteredEntryRide().get(i).getSpeed();
                                    double deltaTime =UserRide.get(j).getFilteredEntryRide().get(i).getTimestamp() - UserRide.get(j).getFilteredEntryRide().get(i-1).getTimestamp();
                                    if (UserRide.get(j).getFilteredEntryRide().get(i).getLng() - UserRide.get(j).getFilteredEntryRide().get(i-1).getLng() != 0) {
                                       UserRide.get(j).getFilteredEntryRide().get(i).setTurn(Math.atan((UserRide.get(j).getFilteredEntryRide().get(i).getLat()-UserRide.get(j).getFilteredEntryRide().get(i-1).getLat())/(UserRide.get(j).getFilteredEntryRide().get(i).getLng()-UserRide.get(j).getFilteredEntryRide().get(i-1).getLng())));
                                        //FilteredArrayTmp[0].setTurn(0);
                                        double deltaTurn = UserRide.get(j).getFilteredEntryRide().get(i).getTurn() - UserRide.get(j).getFilteredEntryRide().get(i-1).getTurn();
                                        double wAcc = Math.abs(deltaTurn/deltaTime);
                                        if ((wAcc < 0.45) && (wAcc >= 0)) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setSevTurn(0);
                                        } else if ((wAcc >= 0.45) && (wAcc < 0.6)) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setSevTurn(1);
                                        } else if ((wAcc >= 0.6) && (wAcc < 0.75)) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setSevTurn(2);
                                        } else if (wAcc >= 0.75) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setSevTurn(3);
                                        }
                                        
                                        double deltaSpeed = speed - UserRide.get(j).getFilteredEntryRide().get(i-1).getSpeed();
                                        double accel = deltaSpeed/deltaTime;

                                        //Высчитываем тип неравномерного движения (ускорение-торможение) через ускорение.
                                        if (accel<-7.5) 
                                            UserRide.get(j).getFilteredEntryRide().get(i).setSevAcc(-3);
                                        else if ((accel>=-7.5)&&(accel<-6)) 
                                            UserRide.get(j).getFilteredEntryRide().get(i).setSevAcc(-2);
                                        else if ((accel>=-6)&&(accel<-4.5))
                                            UserRide.get(j).getFilteredEntryRide().get(i).setSevAcc(-1);
                                        else if (accel>5) 
                                           UserRide.get(j).getFilteredEntryRide().get(i).setSevAcc(3);
                                        else if ((accel>4)&&(accel<=5))
                                            UserRide.get(j).getFilteredEntryRide().get(i).setSevAcc(2);
                                        else if ((accel>3.5)&&(accel<=4))
                                            UserRide.get(j).getFilteredEntryRide().get(i).setSevAcc(1);
                                        else if ((accel>=-4.5)&&(accel<=3.5))
                                            UserRide.get(j).getFilteredEntryRide().get(i).setSevAcc(0);
                                                                    
                                                                    
                                        if ((speed >= 0) && (speed <= 80)) 
                                                UserRide.get(j).getFilteredEntryRide().get(i).setSevSpeed(0);
                                        else if ((speed > 80) && (speed <= 110))
                                            UserRide.get(j).getFilteredEntryRide().get(i).setSevSpeed(1);
                                        else if ((speed > 110) && (speed <= 130))
                                            UserRide.get(j).getFilteredEntryRide().get(i).setSevSpeed(2);
                                        else if (speed > 130)
                                            UserRide.get(j).getFilteredEntryRide().get(i).setSevSpeed(3);
                                    
                                    } else {
                                       UserRide.get(j).getFilteredEntryRide().get(i).setSevTurn(0);
                                        double wAcc = 0;
                                        UserRide.get(j).getFilteredEntryRide().get(i).setTurn(UserRide.get(j).getFilteredEntryRide().get(i-1).getTurn());
                                    }
                                }
                                
                                //Подсчет статистики.
                                
                                //Превышения скоростей:
                                double dss = 0;
                                for(i=1;i< UserRide.get(j).getFilteredEntryRide().size()-1; i++) {
                                    double deltaTime =  UserRide.get(j).getFilteredEntryRide().get(i-1).getTimestamp() -  UserRide.get(j).getFilteredEntryRide().get(i).getTimestamp();
                                    
                                    if ("normal point".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeSpeed())) {
                                        if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed() == 0) {
                                                 UserRide.get(j).getFilteredEntryRide().get(i).setTypeSpeed("normal point");
                                        } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed() == 1) {
                                                 UserRide.get(j).getFilteredEntryRide().get(i).setTypeSpeed("s1");
                                                dss = deltaTime;
                                        } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed() == 2) {
                                                 UserRide.get(j).getFilteredEntryRide().get(i).setTypeSpeed("s2");
                                                dss = deltaTime;
                                        } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed() == 3) {
                                                 UserRide.get(j).getFilteredEntryRide().get(i).setTypeSpeed("s3");
                                                dss = deltaTime;
                                        }
                                    } else if ("s1".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeSpeed())) {
                                        if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed() == 0) {
                                                 UserRide.get(j).getFilteredEntryRide().get(i).setTypeSpeed("normal point");
                                                 UserRide.get(j).setTypeSpeed1Count( UserRide.get(j).getTypeSpeed1Count() + (int)Math.round(dss/3));
                                                dss = 0;
                                        } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed() == 1) {
                                                 UserRide.get(j).getFilteredEntryRide().get(i).setTypeSpeed("s1");
                                                dss += deltaTime;
                                        } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed() == 2) {
                                                 UserRide.get(j).getFilteredEntryRide().get(i).setTypeSpeed("s2");
                                                 UserRide.get(j).setTypeSpeed1Count( UserRide.get(j).getTypeSpeed1Count() + (int)Math.round(dss/3));
                                                dss = 0;
                                        } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed() == 3) {
                                                 UserRide.get(j).getFilteredEntryRide().get(i).setTypeSpeed("s3");
                                                 UserRide.get(j).setTypeSpeed1Count( UserRide.get(j).getTypeSpeed1Count() + (int)Math.round(dss/3));
                                                dss = 0;
                                        }
                                   } else if ("s2".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeSpeed())) {
                                        if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed() == 0) {
                                                 UserRide.get(j).getFilteredEntryRide().get(i).setTypeSpeed("normal point");
                                                 UserRide.get(j).setTypeSpeed2Count( UserRide.get(j).getTypeSpeed2Count() + (int)Math.round(dss/3));
                                                dss = 0;
                                        } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed() == 1) {
                                                 UserRide.get(j).getFilteredEntryRide().get(i).setTypeSpeed("s1");
                                                 UserRide.get(j).setTypeSpeed2Count( UserRide.get(j).getTypeSpeed2Count() + (int)Math.round(dss/3));
                                                dss = 0;
                                        } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed() == 2) {
                                                 UserRide.get(j).getFilteredEntryRide().get(i).setTypeSpeed("s2");
                                                dss += deltaTime;
                                        } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed() == 3) {
                                                 UserRide.get(j).getFilteredEntryRide().get(i).setTypeSpeed("s3");
                                                 UserRide.get(j).setTypeSpeed2Count( UserRide.get(j).getTypeSpeed2Count() + (int)Math.round(dss/3));
                                                dss = 0;
                                        } 
                                   } else if ("s3".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeSpeed())) {
                                        if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed() == 0) {
                                                 UserRide.get(j).getFilteredEntryRide().get(i).setTypeSpeed("normal point");
                                                 UserRide.get(j).setTypeSpeed3Count( UserRide.get(j).getTypeSpeed3Count() + (int)Math.round(dss/3));
                                                dss = 0;
                                        } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed() == 1) {
                                                 UserRide.get(j).getFilteredEntryRide().get(i).setTypeSpeed("s1");
                                                 UserRide.get(j).setTypeSpeed3Count( UserRide.get(j).getTypeSpeed3Count() + (int)Math.round(dss/3));
                                                dss = 0;
                                        } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed() == 2) {
                                                 UserRide.get(j).getFilteredEntryRide().get(i).setTypeSpeed("s2");
                                                 UserRide.get(j).setTypeSpeed3Count( UserRide.get(j).getTypeSpeed3Count() + (int)Math.round(dss/3));
                                                dss = 0;
                                        } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed() == 3) {
                                                 UserRide.get(j).getFilteredEntryRide().get(i).setTypeSpeed("s3");
                                                dss += deltaTime;
                                        }
                                   }
                                   // Конец выявления превышения скоростей.
                                   
                                   //Большое количество проверок условий соотношения ускорений в текущей и прошлой точках.
                                   if ("normal point".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeAcc())) {
                                       if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 0)
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("normal point");
                                       else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 1)
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc1 started");
                                       else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 2)
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc2 started");
                                       else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 3)
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc3 started");
                                       else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -1)
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake1 started");
                                       else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -2)
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake2 started");
                                       else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -3)
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake3 started");
                                   } else if (("acc1 started".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeAcc())) || ("acc1 continued".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeAcc()))) {
                                       if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 0) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("normal point");
                                            UserRide.get(j).setTypeAcc1Count( UserRide.get(j).getTypeAcc1Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 1) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc1 continued");
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 2) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc2 continued");
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 3) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc3 continued");
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -1) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake1 started");
                                            UserRide.get(j).setTypeAcc1Count( UserRide.get(j).getTypeAcc1Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -2) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake2 started");
                                            UserRide.get(j).setTypeAcc2Count( UserRide.get(j).getTypeAcc2Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -3) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake3 continued");
                                            UserRide.get(j).setTypeAcc3Count( UserRide.get(j).getTypeAcc3Count()+1);
                                       }
                                   } else if (("acc2 started".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeAcc())) || ("acc2 continued".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeAcc()))) {
                                       if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 0) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("normal point");
                                            UserRide.get(j).setTypeAcc2Count( UserRide.get(j).getTypeAcc2Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 1) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc1 started");
                                            UserRide.get(j).setTypeAcc2Count( UserRide.get(j).getTypeAcc2Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 2) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc2 continued");
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 3) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc3 continued");
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -1) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake1 started");
                                            UserRide.get(j).setTypeAcc2Count( UserRide.get(j).getTypeAcc2Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -2) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake2 started");
                                            UserRide.get(j).setTypeAcc2Count( UserRide.get(j).getTypeAcc2Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -3) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake3 started");
                                            UserRide.get(j).setTypeAcc2Count( UserRide.get(j).getTypeAcc2Count()+1);
                                       }
                                   } else if (("acc3 started".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeAcc())) || ("acc3 continued".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeAcc()))) {
                                       if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 0) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("normal point");
                                            UserRide.get(j).setTypeAcc3Count( UserRide.get(j).getTypeAcc3Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 1) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc1 started");
                                            UserRide.get(j).setTypeAcc3Count( UserRide.get(j).getTypeAcc3Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 2) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc2 started");
                                            UserRide.get(j).setTypeAcc3Count( UserRide.get(j).getTypeAcc3Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 3) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc3 continued");
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -1) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake1 started");
                                            UserRide.get(j).setTypeAcc3Count( UserRide.get(j).getTypeAcc3Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -2) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake2 started");
                                            UserRide.get(j).setTypeAcc3Count( UserRide.get(j).getTypeAcc3Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -3) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake3 started");
                                            UserRide.get(j).setTypeAcc3Count( UserRide.get(j).getTypeAcc3Count()+1);
                                       }
                                   } else if (("brake1 started".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeAcc())) || ("brake1 continued".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeAcc()))) {
                                       if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 0) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("normal point");
                                            UserRide.get(j).setTypeBrake1Count( UserRide.get(j).getTypeBrake1Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 1) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc1 started");
                                            UserRide.get(j).setTypeBrake1Count( UserRide.get(j).getTypeBrake1Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 2) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc2 started");
                                            UserRide.get(j).setTypeBrake1Count( UserRide.get(j).getTypeBrake1Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 3) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc3 started");
                                            UserRide.get(j).setTypeBrake1Count( UserRide.get(j).getTypeBrake1Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -1) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake1 continued");
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -2) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake2 started");
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -3) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake3 started");
                                       }
                                   }  else if (("brake2 started".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeAcc())) || ("brake2 continued".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeAcc()))) {
                                       if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 0) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("normal point");
                                            UserRide.get(j).setTypeBrake2Count( UserRide.get(j).getTypeBrake2Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 1) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc1 started");
                                            UserRide.get(j).setTypeBrake2Count( UserRide.get(j).getTypeBrake2Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 2) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc2 started");
                                            UserRide.get(j).setTypeBrake2Count( UserRide.get(j).getTypeBrake2Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 3) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc3 started");
                                            UserRide.get(j).setTypeBrake2Count( UserRide.get(j).getTypeBrake2Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -1) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake1 started");
                                            UserRide.get(j).setTypeBrake2Count( UserRide.get(j).getTypeBrake2Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -2) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake2 continued");
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -3) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake3 started");
                                       }
                                   }  else if (("brake3 started".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeAcc())) || ("brake3 continued".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeAcc()))) {
                                       if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 0) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("normal point");
                                            UserRide.get(j).setTypeBrake3Count( UserRide.get(j).getTypeBrake3Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 1) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc1 started");
                                            UserRide.get(j).setTypeBrake3Count( UserRide.get(j).getTypeBrake3Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 2) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc2 started");
                                            UserRide.get(j).setTypeBrake3Count( UserRide.get(j).getTypeBrake3Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == 3) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("acc3 started");
                                            UserRide.get(j).setTypeBrake3Count( UserRide.get(j).getTypeBrake3Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -1) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake1 started");
                                            UserRide.get(j).setTypeBrake3Count( UserRide.get(j).getTypeBrake3Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -2) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake2 started");
                                            UserRide.get(j).setTypeBrake3Count( UserRide.get(j).getTypeBrake3Count()+1);
                                       } else if ( UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc() == -3) {
                                            UserRide.get(j).getFilteredEntryRide().get(i).setTypeAcc("brake3 continued");
                                       }
                                   }
                                   //Конец расчета ускорений/торможений
                                   
                                   //Начало расчета поворотов.
                                   double deltaTurn =  UserRide.get(j).getFilteredEntryRide().get(i).getTurn() -  UserRide.get(j).getFilteredEntryRide().get(i-1).getTurn();
                                   
                                   if (("left turn finished".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeTurn())) 
                                           || ("right turn finished".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeTurn()))
                                           || ( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeTurn() == null)
                                           || ( UserRide.get(j).getFilteredEntryRide().get(i-1).getSpeed() == 0)) {
                                        UserRide.get(j).getFilteredEntryRide().get(i).setTypeTurn("normal point") ;
                                   } else if (deltaTurn > 0.5) {
                                        if ("normal point".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeTurn())) {
                                             UserRide.get(j).getFilteredEntryRide().get(i).setTypeTurn("left turn started");
                                        } else if ("left turn started".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeTurn())
                                                ||("left turn continued".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeTurn()))) {
                                             UserRide.get(j).getFilteredEntryRide().get(i).setTypeTurn("left turn continued");
                                        } else if (("right turn started".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeTurn()))
                                                ||("right turn continued".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeTurn()))) {
                                             UserRide.get(j).getFilteredEntryRide().get(i).setTypeTurn("right turn finished");
                                        }
                                   } else if (deltaTurn < -0.5) {
                                        if ("normal point".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeTurn())) {
                                             UserRide.get(j).getFilteredEntryRide().get(i).setTypeTurn("right turn started");
                                        } else if ("right turn started".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeTurn())
                                                ||("right turn continued".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeTurn()))) {
                                             UserRide.get(j).getFilteredEntryRide().get(i).setTypeTurn("right turn continued");
                                        } else if (("left turn started".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeTurn()))
                                                ||("left turn continued".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeTurn()))) {
                                             UserRide.get(j).getFilteredEntryRide().get(i).setTypeTurn("left turn finished");
                                        }
                                   } else if ((deltaTurn >= -0.5) && (deltaTurn <= 0.5)) {
                                        if ("normal point".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeTurn())) {
                                             UserRide.get(j).getFilteredEntryRide().get(i).setTypeTurn("normal point");
                                        } else if (("left turn started".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeTurn()))
                                                ||("left turn continued".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeTurn()))) {
                                             UserRide.get(j).getFilteredEntryRide().get(i).setTypeTurn("left turn finished");                                    
                                        } else if ("right turn started".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeTurn())||("right turn continued".equals( UserRide.get(j).getFilteredEntryRide().get(i-1).getTypeTurn()))) {
                                             UserRide.get(j).getFilteredEntryRide().get(i).setTypeTurn("right turn continued");
                                        }
                                   }
                                   if (("left turn finished".equals( UserRide.get(j).getFilteredEntryRide().get(i).getTypeTurn())) || (("right turn finished point".equals( UserRide.get(j).getFilteredEntryRide().get(i).getTypeTurn())))) {
                                       switch ( UserRide.get(j).getFilteredEntryRide().get(i).getSevTurn()) {
                                           case 1: {
                                                UserRide.get(j).setTypeTurn1Count( UserRide.get(j).getTypeTurn1Count()+1);
                                               break;
                                           }
                                           case 2: {
                                                UserRide.get(j).setTypeTurn2Count( UserRide.get(j).getTypeTurn2Count()+1);
                                               break;
                                           }
                                           case 3: {
                                                UserRide.get(j).setTypeTurn3Count( UserRide.get(j).getTypeTurn3Count()+1);
                                               break;
                                           }
                                           case 0: {
                                               break;
                                           }
                                       }
                                   }
                                }         
                        }
                }
                          
              //Высчитали параметры каждой отдельной точки
              
              for(int j=0;j<UserRide.size();j++)
                        {
                            if(UserRide.get(j).getFilteredEntryRide().size()>50)
                            {
                            //Сначала создаем новую запись в Id поездки
                            sql="Insert into NTIUserDrivingTrack(UID) values(:UID)";
                            sql_p_main = new NamedParameterStatement(conn, sql);
                            sql_p_main.setInt("UID", UID); 
                            sql_p_main.execute();
                            //Теперь просто получаем Id вставленного 
                             sql="Select Id from NTIUserDrivingTrack where UID=:UID order by Id Desc limit 1";
                            sql_p_main = new NamedParameterStatement(conn, sql);
                            sql_p_main.setInt("UID", UID); 
                           rset= sql_p_main.executeQuery();
                           
                             while (rset.next()) 
                            {
                                        
                                UserTempRoadId=rset.getInt("Id");
                            }
                            sql="Insert into NTIUserDrivingEntry(DrivingID,UID,accx,accy,distance,lat,lng,direction,compass,speed,utimestamp,Blat,Blng,sevAcc,TypeAcc,sevTurn,TurnType,TypeSpeed,sevSpeed) values(:DrivingID,:UID,:accx,:accy,:distance,:lat,:lng,:direction,:compass,:speed,:utimestamp,:Blat,:Blng,:sevAcc,:TypeAcc,:sevTurn,:TurnType,:TypeSpeed,:sevSpeed)";
                            sql_p_main = new NamedParameterStatement(conn, sql); 

                            for(i=1;i< UserRide.get(j).getFilteredEntryRide().size()-1;i++) 
                                {
                                    //Количество поворотов
                                    if(UserRide.get(j).getFilteredEntryRide().get(i).getSevTurn()==1)UserRide.get(j).setTypeTurn1Count(UserRide.get(j).getTypeTurn1Count()+1);
                                    else if(UserRide.get(j).getFilteredEntryRide().get(i).getSevTurn()==2)UserRide.get(j).setTypeTurn2Count(UserRide.get(j).getTypeTurn2Count()+1);
                                    else if(UserRide.get(j).getFilteredEntryRide().get(i).getSevTurn()==3)UserRide.get(j).setTypeTurn3Count(UserRide.get(j).getTypeTurn3Count()+1);
                                //Количество ускорений 
                                    if(UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc()==1)UserRide.get(j).setTypeAcc1Count(UserRide.get(j).getTypeAcc1Count()+1);
                                    else if(UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc()==2)UserRide.get(j).setTypeAcc2Count(UserRide.get(j).getTypeAcc2Count()+1);
                                    else if(UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc()==3)UserRide.get(j).setTypeAcc3Count(UserRide.get(j).getTypeAcc3Count()+1);
                                 //Количество превышений скорости
                                    if(UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed()==1)UserRide.get(j).setTypeSpeed1Count(UserRide.get(j).getTypeSpeed1Count()+1);
                                    else if(UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed()==2)UserRide.get(j).setTypeSpeed2Count(UserRide.get(j).getTypeSpeed2Count()+1);
                                    else if(UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed()==3)UserRide.get(j).setTypeSpeed3Count(UserRide.get(j).getTypeSpeed3Count()+1);
                                  //Количесвто торможений 
                                   if(UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc()==-1)UserRide.get(j).setTypeBrake1Count(UserRide.get(j).getTypeBrake1Count()+1);
                                    else if(UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc()==-2)UserRide.get(j).setTypeBrake2Count(UserRide.get(j).getTypeBrake2Count()+1);
                                    else if(UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc()==-3)UserRide.get(j).setTypeBrake3Count(UserRide.get(j).getTypeBrake3Count()+1);
                                   //Записываем данные точки 
                                   sql_p_main.setObject("DrivingID", UserTempRoadId); 
                                   sql_p_main.setObject("UID", UID);
                                   sql_p_main.setObject("accx", UserRide.get(j).getFilteredEntryRide().get(i).getAccx());
                                   sql_p_main.setObject("accy", UserRide.get(j).getFilteredEntryRide().get(i).getAccy());
                                   sql_p_main.setObject("distance", UserRide.get(j).getFilteredEntryRide().get(i).getDistance());
                                   sql_p_main.setObject("lat", UserRide.get(j).getFilteredEntryRide().get(i).getLat());
                                   sql_p_main.setObject("lng", UserRide.get(j).getFilteredEntryRide().get(i).getLng());
                                   sql_p_main.setObject("direction", UserRide.get(j).getFilteredEntryRide().get(i).getDirection());
                                   sql_p_main.setObject("compass", UserRide.get(j).getFilteredEntryRide().get(i).getCompass());
                                   sql_p_main.setObject("speed", UserRide.get(j).getFilteredEntryRide().get(i).getSpeed());
                                   sql_p_main.setObject("utimestamp", UserRide.get(j).getFilteredEntryRide().get(i).getTimestamp());
                                   sql_p_main.setObject("Blat",0);
                                   sql_p_main.setObject("Blng", 0);
                                   sql_p_main.setObject("sevAcc", UserRide.get(j).getFilteredEntryRide().get(i).getSevAcc());
                                   sql_p_main.setString("TypeAcc", UserRide.get(j).getFilteredEntryRide().get(i).getTypeAcc());
                                   sql_p_main.setObject("sevTurn", UserRide.get(j).getFilteredEntryRide().get(i).getSevTurn());
                                   sql_p_main.setString("TurnType", UserRide.get(j).getFilteredEntryRide().get(i).getTypeTurn());
                                   sql_p_main.setString("TypeSpeed", UserRide.get(j).getFilteredEntryRide().get(i).getTypeSpeed());
                                   sql_p_main.setObject("sevSpeed", UserRide.get(j).getFilteredEntryRide().get(i).getSevSpeed());
                                   sql_p_main.execute();
                                 }  
                       //Высчитали все параметры по поездке , теперь начинаем её записывать   
                       //Теперь запишим статистикув параметры поездки
                            sql="Update NTIUserDrivingTrack set TotalAcc1Count=:TotalAcc1Count,TotalAcc2Count=:TotalAcc2Count,TotalAcc3Count=:TotalAcc3Count,TotalBrake1Count=:TotalBrake1Count,TotalBrake2Count=:TotalBrake2Count,TotalBrake3Count=:TotalBrake3Count,TotalSpeed1Count=:TotalSpeed1Count,TotalSpeed2Count=:TotalSpeed2Count,TotalSpeed3Count=:TotalSpeed3Count,TotalTurn1Count=:TotalTurn1Count,TotalTurn2Count=:TotalTurn2Count,TotalTurn3Count=:TotalTurn3Count,TimeStart=:TimeStart,TimeEnd=:TimeEnd where Id=:Id";
                            sql_p_main = new NamedParameterStatement(conn, sql);
                            sql_p_main.setInt("TotalAcc1Count", UserRide.get(j).getTypeAcc1Count());
                            sql_p_main.setInt("TotalAcc2Count", UserRide.get(j).getTypeAcc2Count());
                            sql_p_main.setInt("TotalAcc3Count", UserRide.get(j).getTypeAcc3Count());
                            sql_p_main.setInt("TotalBrake1Count", UserRide.get(j).getTypeBrake1Count());
                            sql_p_main.setInt("TotalBrake2Count",  UserRide.get(j).getTypeBrake2Count());
                            sql_p_main.setInt("TotalBrake3Count",  UserRide.get(j).getTypeBrake3Count());
                            sql_p_main.setInt("TotalSpeed1Count",  UserRide.get(j).getTypeSpeed1Count());
                            sql_p_main.setInt("TotalSpeed2Count", UserRide.get(j).getTypeSpeed2Count());
                            sql_p_main.setInt("TotalSpeed3Count", UserRide.get(j).getTypeSpeed3Count());
                            sql_p_main.setInt("TotalTurn1Count",  UserRide.get(j).getTypeTurn1Count());
                            sql_p_main.setInt("TotalTurn2Count",  UserRide.get(j).getTypeTurn2Count());
                            sql_p_main.setInt("TotalTurn3Count",  UserRide.get(j).getTypeTurn3Count());
                            sql_p_main.setInt("TimeStart", UserRide.get(j).getTimeStart());
                            sql_p_main.setInt("TimeEnd",  UserRide.get(j).getTimeEnd());
                            sql_p_main.setInt("Id",  UserTempRoadId);
                            sql_p_main.execute();
                       }
               }
            }               
            try {
                conn.close();
            } catch (SQLException ex) {
                Logger.getLogger(runner.class.getName()).log(Level.SEVERE, null, ex);
            }
            conn=null;
        } catch (ClassNotFoundException ex) {
            Logger.getLogger(runner.class.getName()).log(Level.SEVERE, null, ex);
        } catch (SQLException ex) {
            Logger.getLogger(runner.class.getName()).log(Level.SEVERE, null, ex);
        }
                    System.out.println("Task Ended");
        
        
        
        
    }
          
    
    private static double  DistanceBetweenPoints( Double lat1,Double lat2,Double lng1,Double lng2 ) 
    {
  
        return Math.acos(Math.sin(lat1)*Math.sin(lat2)+Math.cos(lat1)*Math.cos(lat2)*Math.cos(lng2-lng1))*111.2;
        
    }
}
