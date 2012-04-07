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
    
    NSMutableArray *dataArray;
}
- (void)addRecord;
- (void)endOfRecord;
- (void)checkWriteRight;
- (void)startOfRecord;

@end
