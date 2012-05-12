/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package junti;

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
    public static void main(String[] args) {
      // ScheduledExecutorService service = Executors.newSingleThreadScheduledExecutor();
        runner executor = new runner();
        executor.update();
       // service.scheduleWithFixedDelay(executor, 0, 120, TimeUnit.SECONDS);
    
    }
}
