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
import java.util.Arrays;
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
            Ride temp_ride;
            ArrayList<Entry> UnfilteredEntry = new ArrayList<Entry>();
            ArrayList<FilteredEntry> UpdatedEntry = new ArrayList<FilteredEntry>();
            ArrayList<Integer> UserList = new ArrayList<Integer>();
            ArrayList<Ride> UserRide=new ArrayList<Ride>();;//Одна поездка пользователя
            FilteredEntry tttt=new FilteredEntry();
            tttt.setLng(1.0);
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
                        if((ArrayEntry[i].getTimestamp()-ArrayEntry[i-1].getTimestamp())<300 && DistanceBetweenPoints(ArrayEntry[i].getLat(),ArrayEntry[i-1].getLat(),ArrayEntry[i].getLng(),ArrayEntry[i-1].getLng())/((ArrayEntry[i].getTimestamp()-ArrayEntry[i-1].getTimestamp()))<200)
                        {
                                    //Добавляем в массив
                                   temp_entry= new Entry();
                                   temp_entry=ArrayEntry[i-1];
                                   UnfilteredEntry.add(temp_entry);
                        }
                        else
                        {
                            temp_ride=new Ride();
                            temp_ride.setEntryRide(UnfilteredEntry);
                            
                            UserRide.add(temp_ride);
                            
                        }
                    }
                    //Теперь удаляем определенно херовые поезки
                    //Если в поездке 0 скоростей больше половины - эт какая-то хуита ребята
                   Ride[] UserRideTmp=new Ride[UserRide.size()];
                   UserRide.toArray(UserRideTmp);
                   UnfilteredCount=UserRide.size();
                   for(int j=0;j<UnfilteredCount;j++)
                    {
                        i=0;
                       
                        for(Entry temptmp : UserRideTmp[j].getEntryRide())
                        {
                           if(temptmp.getSpeed()==0)i++;     
                        }
                        if(i*2<UserRideTmp[j].getEntryRide().size())
                        {
                            UserRideTmp[j]=null;
                        }
                    }
                   //отлично, выкинули все элементы , которые были не нужны
                    //Теперь по каждой поездке, если она не нулевая начинаем высчитывать все данные
                      for(int j=0;j<UnfilteredCount;j++)
                    {
                        if(UserRideTmp[j]!=null)
                        {
                            //Подсчет крутости торможения/ускорения, поворота и скорости точки.
                            for(i=1;i<UserRideTmp[j].getEntryRide().size()-1;i++) 
                            {
                                FilteredEntry[] FilteredArrayTmp = new FilteredEntry[UserRideTmp[j].getEntryRide().size()-1];
				FilteredArrayTmp[i].setSevAcc(0);
                                FilteredArrayTmp[i].setSevTurn(0);
                                FilteredArrayTmp[i].setSevSpeed(0);
                                double speed = FilteredArrayTmp[i].getSpeed();
                                double deltaTime = FilteredArrayTmp[i].getTimestamp() - FilteredArrayTmp[i-1].getTimestamp();
                                //$d += acos(sin($data[$i]['lat'])*sin($data[$i-1]['lat']) + cos($data[$i]['lat'])*cos($data[$i-1]['lat']) *  cos($data[$i-1]['lng']-$data[$i]['lng'])) * 111.2;
                                if (FilteredArrayTmp[i].getLng() - FilteredArrayTmp[i-1].getLng() != 0) {
                                    FilteredArrayTmp[i].setTurn(Math.atan((FilteredArrayTmp[i].getLat()-FilteredArrayTmp[i-1].getLat())/(FilteredArrayTmp[i].getLng()-FilteredArrayTmp[i-1].getLng())));
                                    FilteredArrayTmp[0].setTurn(0);
                                    double deltaTurn = FilteredArrayTmp[i].getTurn() - FilteredArrayTmp[i-1].getTurn();
                                    double wAcc = Math.abs(deltaTurn/deltaTime);
                                    if ((wAcc < 0.45) && (wAcc >= 0)) {
                                        FilteredArrayTmp[i].setSevTurn(0);
                                    } else if ((wAcc >= 0.45) && (wAcc < 0.6)) {
                                        FilteredArrayTmp[i].setSevTurn(1);
                                    } else if ((wAcc >= 0.6) && (wAcc < 0.75)) {
                                        FilteredArrayTmp[i].setSevTurn(2);
                                    } else if (wAcc >= 0.75) {
                                        FilteredArrayTmp[i].setSevTurn(3);
                                    }
                                    
                                    double deltaSpeed = speed - FilteredArrayTmp[i-1].getSpeed();
                                    double accel = deltaSpeed/deltaTime;
                                    //Высчитываем тип неравномерного движения (ускорение-торможение) через ускорение.
                                    if (accel<-7.5) 
					FilteredArrayTmp[i].setSevAcc(-3);
                                    else if ((accel>=-7.5)&&(accel<-6)) 
                                        FilteredArrayTmp[i].setSevAcc(-2);
                                    else if ((accel>=-6)&&(accel<-4.5))
					FilteredArrayTmp[i].setSevAcc(-1);
                                    else if (accel>5) 
                                        FilteredArrayTmp[i].setSevAcc(3);
                                    else if ((accel>4)&&(accel<=5))
                                        FilteredArrayTmp[i].setSevAcc(2);
                                    else if ((accel>3.5)&&(accel<=4))
                                        FilteredArrayTmp[i].setSevAcc(1);
                                    else if ((accel>=-4.5)&&(accel<=3.5))
                                        FilteredArrayTmp[i].setSevAcc(0);
								
								
                                    if ((speed >= 0) && (speed <= 80)) 
                                    	FilteredArrayTmp[i].setSevSpeed(0);
                                    else if ((speed > 80) && (speed <= 110))
					FilteredArrayTmp[i].setSevSpeed(1);
                                    else if ((speed > 110) && (speed <= 130))
					FilteredArrayTmp[i].setSevSpeed(2);
                                    else if (speed > 130)
                                        FilteredArrayTmp[i].setSevSpeed(3);
                                
                                } else {
                                    FilteredArrayTmp[i].setSevTurn(0);
                                    double wAcc = 0;
                                    FilteredArrayTmp[i].setTurn(FilteredArrayTmp[i-1].getTurn());
                                }
                                UserRideTmp[i].setFilteredEntryRide(new ArrayList<FilteredEntry>(Arrays.asList(FilteredArrayTmp)));
                            }
                            
                            //Подсчет статистики.
                            
                            //Превышения скоростей:
                            double dss = 0;
                            for(i=1;i<UserRideTmp[j].getFilteredEntryRide().size()-1; i++) {
                                double deltaTime = UserRideTmp[j].getFilteredEntryRide().get(i-1).getTimestamp() - UserRideTmp[j].getFilteredEntryRide().get(i).getTimestamp();
                                
                                if ("normal point".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeSpeed())) {
                                    if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevSpeed() == 0) {
                                            UserRideTmp[j].getFilteredEntryRide().get(i).setTypeSpeed("normal point");
                                    } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevSpeed() == 1) {
                                            UserRideTmp[j].getFilteredEntryRide().get(i).setTypeSpeed("s1");
                                            dss = deltaTime;
                                    } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevSpeed() == 2) {
                                            UserRideTmp[j].getFilteredEntryRide().get(i).setTypeSpeed("s2");
                                            dss = deltaTime;
                                    } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevSpeed() == 3) {
                                            UserRideTmp[j].getFilteredEntryRide().get(i).setTypeSpeed("s3");
                                            dss = deltaTime;
                                    }
                                } else if ("s1".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeSpeed())) {
                                    if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevSpeed() == 0) {
                                            UserRideTmp[j].getFilteredEntryRide().get(i).setTypeSpeed("normal point");
                                            UserRideTmp[j].setTypeSpeed1Count(UserRideTmp[j].getTypeSpeed1Count() + (int)Math.round(dss/3));
                                            dss = 0;
                                    } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevSpeed() == 1) {
                                            UserRideTmp[j].getFilteredEntryRide().get(i).setTypeSpeed("s1");
                                            dss += deltaTime;
                                    } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevSpeed() == 2) {
                                            UserRideTmp[j].getFilteredEntryRide().get(i).setTypeSpeed("s2");
                                            UserRideTmp[j].setTypeSpeed1Count(UserRideTmp[j].getTypeSpeed1Count() + (int)Math.round(dss/3));
                                            dss = 0;
                                    } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevSpeed() == 3) {
                                            UserRideTmp[j].getFilteredEntryRide().get(i).setTypeSpeed("s3");
                                            UserRideTmp[j].setTypeSpeed1Count(UserRideTmp[j].getTypeSpeed1Count() + (int)Math.round(dss/3));
                                            dss = 0;
                                    }
                               } else if ("s2".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeSpeed())) {
                                    if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevSpeed() == 0) {
                                            UserRideTmp[j].getFilteredEntryRide().get(i).setTypeSpeed("normal point");
                                            UserRideTmp[j].setTypeSpeed2Count(UserRideTmp[j].getTypeSpeed2Count() + (int)Math.round(dss/3));
                                            dss = 0;
                                    } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevSpeed() == 1) {
                                            UserRideTmp[j].getFilteredEntryRide().get(i).setTypeSpeed("s1");
                                            UserRideTmp[j].setTypeSpeed2Count(UserRideTmp[j].getTypeSpeed2Count() + (int)Math.round(dss/3));
                                            dss = 0;
                                    } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevSpeed() == 2) {
                                            UserRideTmp[j].getFilteredEntryRide().get(i).setTypeSpeed("s2");
                                            dss += deltaTime;
                                    } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevSpeed() == 3) {
                                            UserRideTmp[j].getFilteredEntryRide().get(i).setTypeSpeed("s3");
                                            UserRideTmp[j].setTypeSpeed2Count(UserRideTmp[j].getTypeSpeed2Count() + (int)Math.round(dss/3));
                                            dss = 0;
                                    } 
                               } else if ("s3".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeSpeed())) {
                                    if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevSpeed() == 0) {
                                            UserRideTmp[j].getFilteredEntryRide().get(i).setTypeSpeed("normal point");
                                            UserRideTmp[j].setTypeSpeed3Count(UserRideTmp[j].getTypeSpeed3Count() + (int)Math.round(dss/3));
                                            dss = 0;
                                    } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevSpeed() == 1) {
                                            UserRideTmp[j].getFilteredEntryRide().get(i).setTypeSpeed("s1");
                                            UserRideTmp[j].setTypeSpeed3Count(UserRideTmp[j].getTypeSpeed3Count() + (int)Math.round(dss/3));
                                            dss = 0;
                                    } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevSpeed() == 2) {
                                            UserRideTmp[j].getFilteredEntryRide().get(i).setTypeSpeed("s2");
                                            UserRideTmp[j].setTypeSpeed3Count(UserRideTmp[j].getTypeSpeed3Count() + (int)Math.round(dss/3));
                                            dss = 0;
                                    } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevSpeed() == 3) {
                                            UserRideTmp[j].getFilteredEntryRide().get(i).setTypeSpeed("s3");
                                            dss += deltaTime;
                                    }
                               }
                               // Конец выявления превышения скоростей.
                               
                               //Большое количество проверок условий соотношения ускорений в текущей и прошлой точках.
                               if ("normal point".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeAcc())) {
                                   if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 0)
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("normal point");
                                   else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 1)
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc1 started");
                                   else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 2)
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc2 started");
                                   else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 3)
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc3 started");
                                   else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -1)
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake1 started");
                                   else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -2)
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake2 started");
                                   else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -3)
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake3 started");
                               } else if (("acc1 started".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeAcc())) || ("acc1 continued".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeAcc()))) {
                                   if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 0) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("normal point");
                                       UserRideTmp[j].setTypeAcc1Count(UserRideTmp[j].getTypeAcc1Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 1) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc1 continued");
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 2) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc2 continued");
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 3) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc3 continued");
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -1) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake1 started");
                                       UserRideTmp[j].setTypeAcc1Count(UserRideTmp[j].getTypeAcc1Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -2) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake2 started");
                                       UserRideTmp[j].setTypeAcc2Count(UserRideTmp[j].getTypeAcc2Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -3) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake3 continued");
                                       UserRideTmp[j].setTypeAcc3Count(UserRideTmp[j].getTypeAcc3Count()+1);
                                   }
                               } else if (("acc2 started".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeAcc())) || ("acc2 continued".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeAcc()))) {
                                   if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 0) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("normal point");
                                       UserRideTmp[j].setTypeAcc2Count(UserRideTmp[j].getTypeAcc2Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 1) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc1 started");
                                       UserRideTmp[j].setTypeAcc2Count(UserRideTmp[j].getTypeAcc2Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 2) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc2 continued");
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 3) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc3 continued");
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -1) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake1 started");
                                       UserRideTmp[j].setTypeAcc2Count(UserRideTmp[j].getTypeAcc2Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -2) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake2 started");
                                       UserRideTmp[j].setTypeAcc2Count(UserRideTmp[j].getTypeAcc2Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -3) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake3 started");
                                       UserRideTmp[j].setTypeAcc2Count(UserRideTmp[j].getTypeAcc2Count()+1);
                                   }
                               } else if (("acc3 started".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeAcc())) || ("acc3 continued".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeAcc()))) {
                                   if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 0) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("normal point");
                                       UserRideTmp[j].setTypeAcc3Count(UserRideTmp[j].getTypeAcc3Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 1) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc1 started");
                                       UserRideTmp[j].setTypeAcc3Count(UserRideTmp[j].getTypeAcc3Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 2) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc2 started");
                                       UserRideTmp[j].setTypeAcc3Count(UserRideTmp[j].getTypeAcc3Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 3) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc3 continued");
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -1) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake1 started");
                                       UserRideTmp[j].setTypeAcc3Count(UserRideTmp[j].getTypeAcc3Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -2) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake2 started");
                                       UserRideTmp[j].setTypeAcc3Count(UserRideTmp[j].getTypeAcc3Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -3) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake3 started");
                                       UserRideTmp[j].setTypeAcc3Count(UserRideTmp[j].getTypeAcc3Count()+1);
                                   }
                               } else if (("brake1 started".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeAcc())) || ("brake1 continued".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeAcc()))) {
                                   if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 0) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("normal point");
                                       UserRideTmp[j].setTypeBrake1Count(UserRideTmp[j].getTypeBrake1Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 1) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc1 started");
                                       UserRideTmp[j].setTypeBrake1Count(UserRideTmp[j].getTypeBrake1Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 2) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc2 started");
                                       UserRideTmp[j].setTypeBrake1Count(UserRideTmp[j].getTypeBrake1Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 3) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc3 started");
                                       UserRideTmp[j].setTypeBrake1Count(UserRideTmp[j].getTypeBrake1Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -1) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake1 continued");
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -2) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake2 started");
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -3) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake3 started");
                                   }
                               }  else if (("brake2 started".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeAcc())) || ("brake2 continued".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeAcc()))) {
                                   if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 0) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("normal point");
                                       UserRideTmp[j].setTypeBrake2Count(UserRideTmp[j].getTypeBrake2Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 1) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc1 started");
                                       UserRideTmp[j].setTypeBrake2Count(UserRideTmp[j].getTypeBrake2Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 2) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc2 started");
                                       UserRideTmp[j].setTypeBrake2Count(UserRideTmp[j].getTypeBrake2Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 3) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc3 started");
                                       UserRideTmp[j].setTypeBrake2Count(UserRideTmp[j].getTypeBrake2Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -1) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake1 started");
                                       UserRideTmp[j].setTypeBrake2Count(UserRideTmp[j].getTypeBrake2Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -2) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake2 continued");
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -3) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake3 started");
                                   }
                               }  else if (("brake3 started".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeAcc())) || ("brake3 continued".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeAcc()))) {
                                   if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 0) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("normal point");
                                       UserRideTmp[j].setTypeBrake3Count(UserRideTmp[j].getTypeBrake3Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 1) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc1 started");
                                       UserRideTmp[j].setTypeBrake3Count(UserRideTmp[j].getTypeBrake3Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 2) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc2 started");
                                       UserRideTmp[j].setTypeBrake3Count(UserRideTmp[j].getTypeBrake3Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == 3) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("acc3 started");
                                       UserRideTmp[j].setTypeBrake3Count(UserRideTmp[j].getTypeBrake3Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -1) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake1 started");
                                       UserRideTmp[j].setTypeBrake3Count(UserRideTmp[j].getTypeBrake3Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -2) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake2 started");
                                       UserRideTmp[j].setTypeBrake3Count(UserRideTmp[j].getTypeBrake3Count()+1);
                                   } else if (UserRideTmp[j].getFilteredEntryRide().get(i).getSevAcc() == -3) {
                                       UserRideTmp[j].getFilteredEntryRide().get(i).setTypeAcc("brake3 continued");
                                   }
                               }
                               //Конец расчета ускорений/торможений
                               
                               //Начало расчета поворотов.
                               double deltaTurn = UserRideTmp[j].getFilteredEntryRide().get(i).getTurn() - UserRideTmp[j].getFilteredEntryRide().get(i-1).getTurn();
                               
                               if (("left turn finished".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeTurn())) 
                                       || ("right turn finished".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeTurn()))
                                       || (UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeTurn() == null)
                                       || (UserRideTmp[j].getFilteredEntryRide().get(i-1).getSpeed() == 0)) {
                                   UserRideTmp[j].getFilteredEntryRide().get(i).setTypeTurn("normal point") ;
                               } else if (deltaTurn > 0.5) {
                                    if ("normal point".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeTurn())) {
                                        UserRideTmp[j].getFilteredEntryRide().get(i).setTypeTurn("left turn started");
                                    } else if ("left turn started".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeTurn())
                                            ||("left turn continued".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeTurn()))) {
					UserRideTmp[j].getFilteredEntryRide().get(i).setTypeTurn("left turn continued");
                                    } else if (("right turn started".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeTurn()))
                                            ||("right turn continued".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeTurn()))) {
                                        UserRideTmp[j].getFilteredEntryRide().get(i).setTypeTurn("right turn finished");
                                    }
                               } else if (deltaTurn < -0.5) {
                                    if ("normal point".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeTurn())) {
                                        UserRideTmp[j].getFilteredEntryRide().get(i).setTypeTurn("right turn started");
                                    } else if ("right turn started".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeTurn())
                                            ||("right turn continued".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeTurn()))) {
					UserRideTmp[j].getFilteredEntryRide().get(i).setTypeTurn("right turn continued");
                                    } else if (("left turn started".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeTurn()))
                                            ||("left turn continued".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeTurn()))) {
                                        UserRideTmp[j].getFilteredEntryRide().get(i).setTypeTurn("left turn finished");
                                    }
                               } else if ((deltaTurn >= -0.5) && (deltaTurn <= 0.5)) {
                                    if ("normal point".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeTurn())) {
                                        UserRideTmp[j].getFilteredEntryRide().get(i).setTypeTurn("normal point");
                                    } else if (("left turn started".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeTurn()))
                                            ||("left turn continued".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeTurn()))) {
                                        UserRideTmp[j].getFilteredEntryRide().get(i).setTypeTurn("left turn finished");                                    
                                    } else if ("right turn started".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeTurn())||("right turn continued".equals(UserRideTmp[j].getFilteredEntryRide().get(i-1).getTypeTurn()))) {
					UserRideTmp[j].getFilteredEntryRide().get(i).setTypeTurn("right turn continued");
                                    }
                               }
                               if (("left turn finished".equals(UserRideTmp[j].getFilteredEntryRide().get(i).getTypeTurn())) || (("right turn finished point".equals(UserRideTmp[j].getFilteredEntryRide().get(i).getTypeTurn())))) {
                                   switch (UserRideTmp[j].getFilteredEntryRide().get(i).getSevTurn()) {
                                       case 1: {
                                           UserRideTmp[j].setTypeTurn1Count(UserRideTmp[j].getTypeTurn1Count()+1);
                                           break;
                                       }
                                       case 2: {
                                           UserRideTmp[j].setTypeTurn2Count(UserRideTmp[j].getTypeTurn2Count()+1);
                                           break;
                                       }
                                       case 3: {
                                           UserRideTmp[j].setTypeTurn3Count(UserRideTmp[j].getTypeTurn3Count()+1);
                                           break;
                                       }
                                       case 0: {
                                           break;
                                       }
                                   }
                               }
                               //Конец расчета поворотов.
                            }
                            //Конец подсчета статистики
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
