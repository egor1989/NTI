/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package junti;

import java.sql.SQLException;
import java.util.concurrent.Executors;
import java.util.concurrent.ScheduledExecutorService;
import java.util.concurrent.TimeUnit;

/**
 *
 * @author steph
 */
public class JUNTI {

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) throws SQLException, ClassNotFoundException {
       ScheduledExecutorService service = Executors.newSingleThreadScheduledExecutor();
        runner executor = new runner();
        service.scheduleWithFixedDelay(executor, 0, 3600, TimeUnit.SECONDS);
    }
}
