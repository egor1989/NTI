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
     private ArrayList<FilteredEntry> FilteredEntryRide;
     
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
     private Integer TypeBrake1Count;
     private Integer TypeBrake2Count;
     private Integer TypeBrake3Count;
     private Double TotalDistance;
      public Ride() 
      {
      TypeAcc1Count=0;
      TimeStart=0;
        TimeEnd    =0; 
       TypeAcc1Count  =0;
        TypeAcc2Count =0;       
        TypeAcc3Count =0;       
        TypeTurn1Count=0;       
        TypeTurn2Count=0;       
        TypeTurn3Count =0;      
        TypeSpeed1Count =0;      
        TypeSpeed2Count =0;     
        TypeSpeed3Count =0;     
        TypeBrake1Count =0;     
        TypeBrake2Count =0;    
        TypeBrake3Count =0;    
        TotalDistance   =0.0;  
        FilteredEntryRide=new  ArrayList<FilteredEntry>();
              
      }

    public Ride(Ride ride) {
        throw new UnsupportedOperationException("Not yet implemented");
    }
    /**
     * @return the Entry
     */

    /**
     * @return the Score
     */

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

    /**
     * @return the EntryRide
     */

    /**
     * @return the FilteredEntryRide
     */
    public ArrayList<FilteredEntry> getFilteredEntryRide() {
        return FilteredEntryRide;
    }

    /**
     * @param FilteredEntryRide the FilteredEntryRide to set
     */
    public void setFilteredEntryRide(ArrayList<FilteredEntry> FilteredEntryRide) {
        this.FilteredEntryRide = FilteredEntryRide;
    }

    /**
     * @return the TypeBrake1Count
     */
    public Integer getTypeBrake1Count() {
        return TypeBrake1Count;
    }

    /**
     * @param TypeBrake1Count the TypeBrake1Count to set
     */
    public void setTypeBrake1Count(Integer TypeBrake1Count) {
        this.TypeBrake1Count = TypeBrake1Count;
    }

    /**
     * @return the TypeBrake2Count
     */
    public Integer getTypeBrake2Count() {
        return TypeBrake2Count;
    }

    /**
     * @param TypeBrake2Count the TypeBrake2Count to set
     */
    public void setTypeBrake2Count(Integer TypeBrake2Count) {
        this.TypeBrake2Count = TypeBrake2Count;
    }

    /**
     * @return the TypeBrake3Count
     */
    public Integer getTypeBrake3Count() {
        return TypeBrake3Count;
    }

    /**
     * @param TypeBrake3Count the TypeBrake3Count to set
     */
    public void setTypeBrake3Count(Integer TypeBrake3Count) {
        this.TypeBrake3Count = TypeBrake3Count;
    }

    public void setTimeStart(double timestamp) {
        this.TimeStart=(int)timestamp;
    }

    public void setTimeEnd(double timestamp) {
         this.TimeEnd=(int)timestamp;
    }

}
