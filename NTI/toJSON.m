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
        [[NSNotificationCenter defaultCenter]	
         addObserver: self
         selector: @selector(convert)
         name: @"convertToJSON"
         object: nil]; 
        
    }
    return self;
}

- (void)convert{
    
    NSLog(@"convert");
   // NSArray *forConvert = [
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
    NSData* jsonData = [NSJSONSerialization dataWithJSONObject:info 
                                                       options:NSJSONWritingPrettyPrinted error:&error];
   */
}



@end
