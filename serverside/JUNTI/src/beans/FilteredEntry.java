/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package beans;

/**
 *
 * @author steph
 */
public class FilteredEntry  extends Entry{
       

       private String TypeAcc;
       private String TypeTurn;
       private String TypeSpeed;
       private int sevAcc;
       private int sevTurn;
       private int sevSpeed;

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
       
       
       
}
