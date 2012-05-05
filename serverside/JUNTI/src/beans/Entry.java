/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package beans;

/**
 *
 * @author steph
 */
public class Entry {

       private Integer Id;
       private double accx;
       private double accy;
       private double distance;
       private double lat;
       private double lng;
       private double direction;
       private double compass;
       private double speed;
       private double timestamp;
       private int UID;

    /**
     * @return the accx
     */
    public double getAccx() {
        return accx;
    }

    /**
     * @param accx the accx to set
     */
    public void setAccx(double accx) {
        this.accx = accx;
    }

    /**
     * @return the accy
     */
    public double getAccy() {
        return accy;
    }

    /**
     * @param accy the accy to set
     */
    public void setAccy(double accy) {
        this.accy = accy;
    }

    /**
     * @return the distance
     */
    public double getDistance() {
        return distance;
    }

    /**
     * @param distance the distance to set
     */
    public void setDistance(double distance) {
        this.distance = distance;
    }

    /**
     * @return the lat
     */
    public double getLat() {
        return lat;
    }

    /**
     * @param lat the lat to set
     */
    public void setLat(double lat) {
        this.lat = lat;
    }

    /**
     * @return the lng
     */
    public double getLng() {
        return lng;
    }

    /**
     * @param lng the lng to set
     */
    public void setLng(double lng) {
        this.lng = lng;
    }

    /**
     * @return the direction
     */
    public double getDirection() {
        return direction;
    }

    /**
     * @param direction the direction to set
     */
    public void setDirection(double direction) {
        this.direction = direction;
    }

    /**
     * @return the compass
     */
    public double getCompass() {
        return compass;
    }

    /**
     * @param compass the compass to set
     */
    public void setCompass(double compass) {
        this.compass = compass;
    }

    /**
     * @return the speed
     */
    public double getSpeed() {
        return speed;
    }

    /**
     * @param speed the speed to set
     */
    public void setSpeed(double speed) {
        this.speed = speed;
    }

    /**
     * @return the timestamp
     */
    public double getTimestamp() {
        return timestamp;
    }

    /**
     * @param timestamp the timestamp to set
     */
    public void setTimestamp(double timestamp) {
        this.timestamp = timestamp;
    }

    /**
     * @return the UID
     */
    public int getUID() {
        return UID;
    }

    /**
     * @param UID the UID to set
     */
    public void setUID(int UID) {
        this.UID = UID;
    }

    /**
     * @return the Id
     */
    public Integer getId() {
        return Id;
    }

    /**
     * @param Id the Id to set
     */
    public void setId(Integer Id) {
        this.Id = Id;
    }
       
}
