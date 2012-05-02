/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package beans;

import java.util.ArrayList;

/**
 *
 * @author steph
 */
public class Ride {
     private ArrayList<FilteredEntry> Entry;
     private Integer Score;
     private Integer TimeStart;
     private Integer TimeEnd;
     private Integer TypeAcc1Count;
     private Integer TypeAcc2Count;
     private Integer TypeAcc3Count;
     private Integer TypeTurn1Count;
     private Integer TypeTurn2Count;
     private Integer TypeTurn3Count;
     private Integer TypeSpeed1Count;
     private Integer TypeSpeed2Count;
     private Integer TypeSpeed3Count;
     private Double TotalDistance;

    /**
     * @return the Entry
     */
    public ArrayList<FilteredEntry> getEntry() {
        return Entry;
    }

    /**
     * @param Entry the Entry to set
     */
    public void setEntry(ArrayList<FilteredEntry> Entry) {
        this.Entry = Entry;
    }
    //Добавляет новую точку в список
    public void addEntry(FilteredEntry Temp) {
        this.Entry.add(Temp);
    }
    /**
     * @return the Score
     */
    public Integer getScore() {
        return Score;
    }

    /**
     * @param Score the Score to set
     */
    public void setScore(Integer Score) {
        this.Score = Score;
    }

    /**
     * @return the TimeStart
     */
    public Integer getTimeStart() {
        return TimeStart;
    }

    /**
     * @param TimeStart the TimeStart to set
     */
    public void setTimeStart(Integer TimeStart) {
        this.TimeStart = TimeStart;
    }

    /**
     * @return the TimeEnd
     */
    public Integer getTimeEnd() {
        return TimeEnd;
    }

    /**
     * @param TimeEnd the TimeEnd to set
     */
    public void setTimeEnd(Integer TimeEnd) {
        this.TimeEnd = TimeEnd;
    }

    /**
     * @return the TypeAcc1Count
     */
    public Integer getTypeAcc1Count() {
        return TypeAcc1Count;
    }

    /**
     * @param TypeAcc1Count the TypeAcc1Count to set
     */
    public void setTypeAcc1Count(Integer TypeAcc1Count) {
        this.TypeAcc1Count = TypeAcc1Count;
    }

    /**
     * @return the TypeAcc2Count
     */
    public Integer getTypeAcc2Count() {
        return TypeAcc2Count;
    }

    /**
     * @param TypeAcc2Count the TypeAcc2Count to set
     */
    public void setTypeAcc2Count(Integer TypeAcc2Count) {
        this.TypeAcc2Count = TypeAcc2Count;
    }

    /**
     * @return the TypeAcc3Count
     */
    public Integer getTypeAcc3Count() {
        return TypeAcc3Count;
    }

    /**
     * @param TypeAcc3Count the TypeAcc3Count to set
     */
    public void setTypeAcc3Count(Integer TypeAcc3Count) {
        this.TypeAcc3Count = TypeAcc3Count;
    }

    /**
     * @return the TypeTurn1Count
     */
    public Integer getTypeTurn1Count() {
        return TypeTurn1Count;
    }

    /**
     * @param TypeTurn1Count the TypeTurn1Count to set
     */
    public void setTypeTurn1Count(Integer TypeTurn1Count) {
        this.TypeTurn1Count = TypeTurn1Count;
    }

    /**
     * @return the TypeTurn2Count
     */
    public Integer getTypeTurn2Count() {
        return TypeTurn2Count;
    }

    /**
     * @param TypeTurn2Count the TypeTurn2Count to set
     */
    public void setTypeTurn2Count(Integer TypeTurn2Count) {
        this.TypeTurn2Count = TypeTurn2Count;
    }

    /**
     * @return the TypeTurn3Count
     */
    public Integer getTypeTurn3Count() {
        return TypeTurn3Count;
    }

    /**
     * @param TypeTurn3Count the TypeTurn3Count to set
     */
    public void setTypeTurn3Count(Integer TypeTurn3Count) {
        this.TypeTurn3Count = TypeTurn3Count;
    }

    /**
     * @return the TypeSpeed1Count
     */
    public Integer getTypeSpeed1Count() {
        return TypeSpeed1Count;
    }

    /**
     * @param TypeSpeed1Count the TypeSpeed1Count to set
     */
    public void setTypeSpeed1Count(Integer TypeSpeed1Count) {
        this.TypeSpeed1Count = TypeSpeed1Count;
    }

    /**
     * @return the TypeSpeed2Count
     */
    public Integer getTypeSpeed2Count() {
        return TypeSpeed2Count;
    }

    /**
     * @param TypeSpeed2Count the TypeSpeed2Count to set
     */
    public void setTypeSpeed2Count(Integer TypeSpeed2Count) {
        this.TypeSpeed2Count = TypeSpeed2Count;
    }

    /**
     * @return the TypeSpeed3Count
     */
    public Integer getTypeSpeed3Count() {
        return TypeSpeed3Count;
    }

    /**
     * @param TypeSpeed3Count the TypeSpeed3Count to set
     */
    public void setTypeSpeed3Count(Integer TypeSpeed3Count) {
        this.TypeSpeed3Count = TypeSpeed3Count;
    }

    /**
     * @return the TotalDistance
     */
    public Double getTotalDistance() {
        return TotalDistance;
    }

    /**
     * @param TotalDistance the TotalDistance to set
     */
    public void setTotalDistance(Double TotalDistance) {
        this.TotalDistance = TotalDistance;
    }
     
}
