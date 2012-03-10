//
//  Record.m
//  NTI
//
//  Created by Елена on 10.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "Record.h"

@implementation Record


- (id)initRecordWithType: (NSString *)typeTmp Timestamp: (double)timeTmp AccX: (double)accXTmp AccY: (double)accYTmp Compass:(double)compassTmp Direction: (double)directionTmp Distance: (double)distanceTmp Latitude: (double)latitudeTmp Longitude:(double)longitudeTmp Speed: (double)speedTmp  {
    self = [super init];
    type = typeTmp;
    time = timeTmp;
    accX = accXTmp;
    accY = accYTmp;
    compass = compassTmp;
    direction = directionTmp;
    distance = distanceTmp;
    latitude = latitudeTmp;
    longitude = longitudeTmp;
    speed = speedTmp;
    return self;
}



@end
