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

- (void)convert : (NSArray *)arrayForConvert{
    // keys = [NSArray arrayWithObjects:@"timestamp", @"acX", @"acY",@"gpsSpeed",@"gpsCourse", nil];

    NSLog(@"convert");
    NSLog(@"count %i", arrayForConvert.count);
    

    NSError *error = nil;
    NSData  *jsonArray = [NSJSONSerialization dataWithJSONObject:arrayForConvert options:NSJSONWritingPrettyPrinted error:&error]; 
    
    NSString *JSON = [[NSString alloc] initWithData:jsonArray encoding:NSASCIIStringEncoding]; 
    NSLog(@"JSON %@", JSON);
   // NSArray *acc = [NSArray arrayWithObject:[arrayForConvert objectAtIndex:],[],nil];
    
  /*
    //build an info object and convert to json
    NSDictionary* info = [NSDictionary dictionaryWithObjectsAndKeys:
                          [loan objectForKey:@"name"], 
                          @"who",
                          [(NSDictionary*)[loan objectForKey:@"location"] 
                           objectForKey:@"country"], 
                          @"where",
                          [NSNumber numberWithFloat: outstandingAmount], 
                          @"what",
                          nil];
    
    //convert object to data
   */
 //   NSData* jsonData = [NSJSONSerialization dataWithJSONObject:arrayForConvert 
 //                                                      options:NSJSONWritingPrettyPrinted error:&error];
   
}



@end
