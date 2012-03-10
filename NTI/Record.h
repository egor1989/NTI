//
//  Record.h
//  NTI
//
//  Created by Елена on 10.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface Record : NSObject{
    NSString *type;
    double time;
    double accX;
    double accY;
    double compass;
    double direction;
    double distance;
    double latitude;
    double longitude;
    double speed;
    /*

     
     - (id)initWithUserID:(int)userID 
     UserName:(NSString*)userName 
     Time:(double)time 
     Speed:(double)speed 
     Longitude:(double)longitude 
     Latitude:(double)latitude 
     Altitude:(double)altitude;
     */
}
- (id)initRecordWithType: (NSString *)typeTmp 
               Timestamp: (double)timeTmp 
                AccX: (double)accXTmp 
                AccY: (double)accYTmp
                Compass:(double)compassTmp 
                Direction: (double)directionTmp 
                Distance: (double)distanceTmp 
                Latitude: (double)latitudeTmp 
                Longitude:(double)longitudeTmp 
                Speed: (double)speedTmp;

@end
