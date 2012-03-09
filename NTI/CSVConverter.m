//
//  CSVConverter.m
//  NTI
//
//  Created by Елена on 03.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "CSVConverter.h"

@implementation CSVConverter



- (NSString *) arrayToCSVString: (NSMutableArray *) arrayForConvert{
    NSLog(@"data = ", arrayForConvert);
    NSString *csv = @"";
    
    for (NSDictionary *entries in arrayForConvert) {

        NSDictionary *gps = [entries objectForKey:@"gps"];

        NSString *string = [NSString stringWithFormat: @"%@,%@,%@,%@,%@,%@,\n",[gps objectForKey:@"latitude"],[gps objectForKey:@"longitude"],[gps objectForKey:@"compass"],[gps objectForKey:@"speed"],[gps objectForKey:@"distance"], [entries objectForKey:@"timestamp"]];
        NSLog(@"%@",string);
        
        csv = [csv stringByAppendingString:string];
        
       // NSLog(@"%@",csv);

    }
    
    return csv;
    
}

@end