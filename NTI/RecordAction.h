//
//  RecordAction.h
//  NTI
//
//  Created by Елена on 07.04.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>

#import "DatabaseActions.h"


@interface RecordAction : NSObject {
    NSDictionary *accelDict;
    double x,y;
    DatabaseActions *databaseAction;
    NSMutableArray *toWrite;
    NSMutableArray *dataArray;
    BOOL Start;
    BOOL End;
    BOOL First;
}
- (void)addRecord;
- (void)endOfRecord;
- (void)checkWriteRight;
- (void)startOfRecord;
- (void)sendFile;
- (void)eventRecord: (NSString *)type;
- (void)checkActivity;


@end
