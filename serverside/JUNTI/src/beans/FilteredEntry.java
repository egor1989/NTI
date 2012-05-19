/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package beans;

/**
 *
 * @author steph
 */
public class FilteredEntry  extends Entry {
       

       private String TypeAcc;
       private String TypeTurn;
       private String TypeSpeed;
       private int sevAcc;
       private int sevTurn;
       private int sevSpeed;
       private double turn;
       private double accel;

    public FilteredEntry(double aDouble) {
 
        
    }

    public FilteredEntry(double Accx, double Accy, double Compass, double Direction, double Distance, double Lat, double Lng, double Speed, double UtimeStamp, int UID, int Id) {
        
        this.setAccx(Accx);
        this.setAccy(Accy);
        this.setLat(Lat);
        this.setLng(Lng);
        this.setCompass(Compass);
        this.setDirection(Direction);
        this.setDistance(Distance);
        this.setId(Id);
        this.setTimestamp(UtimeStamp);
        this.setUID(UID);

    }
       
    
       
    /**
     * @return the TypeAcc
     */
    public String getTypeAcc() {
        return TypeAcc;
    }

    /**
     * @param TypeAcc the TypeAcc to set
     */
    public void setTypeAcc(String TypeAcc) {
        this.TypeAcc = TypeAcc;
    }

    /**
     * @return the TypeTurn
     */
    public String getTypeTurn() {
        return TypeTurn;
    }

    /**
     * @param TypeTurn the TypeTurn to set
     */
    public void setTypeTurn(String TypeTurn) {
        this.TypeTurn = TypeTurn;
    }

    /**
     * @return the TypeSpeed
     */
    public String getTypeSpeed() {
        return TypeSpeed;
    }

    /**
     * @param TypeSpeed the TypeSpeed to set
     */
    public void setTypeSpeed(String TypeSpeed) {
        this.TypeSpeed = TypeSpeed;
    }

    /**
     * @return the sevAcc
     */
    public int getSevAcc() {
        return sevAcc;
    }

    /**
     * @param sevAcc the sevAcc to set
     */
    public void setSevAcc(int sevAcc) {
        this.sevAcc = sevAcc;
    }

    /**
     * @return the sevTurn
     */
    public int getSevTurn() {
        return sevTurn;
    }

    /**
     * @param sevTurn the sevTurn to set
     */
    public void setSevTurn(int sevTurn) {
        this.sevTurn = sevTurn;
    }

    /**
     * @return the sevSpeed
     */
    public int getSevSpeed() {
        return sevSpeed;
    }

    /**
     * @param sevSpeed the sevSpeed to set
     */
    public void setSevSpeed(int sevSpeed) {
        this.sevSpeed = sevSpeed;
    }
       
    public double getTurn() {
        return turn;
    }        
    
    public void setTurn(double turn) {
        this.turn = turn;
    }

    public double getAccel() {
        return accel;
    }        
    
    public void setAccel(double accel) {
        this.accel = accel;
    }

       
       
}
