//
//  toJSON.m
//  NTI
//
//  Created by Елена on 08.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "toJSON.h"

@implementation toJSON

- (id) init {
    self = [super init];
    
    if (self != nil) {
                
    }
    return self;
}

- (NSString *)convert : (NSArray *)arrayForConvert{
    // keys = [NSArray arrayWithObjects:@"timestamp", @"acX", @"acY",@"gpsSpeed",@"gpsCourse", nil];

    NSLog(@"convert");
    NSLog(@"count %i", arrayForConvert.count);
    

    NSError *error = nil;
    NSData  *jsonArray = [NSJSONSerialization dataWithJSONObject:arrayForConvert options:NSJSONWritingPrettyPrinted error:&error]; 
    
    NSString *JSON = [[NSString alloc] initWithData:jsonArray encoding:NSASCIIStringEncoding]; 
    NSLog(@"JSON %@", JSON);
    
    FileController *fileController = [[FileController alloc] init];
    
    
   
}

@end
