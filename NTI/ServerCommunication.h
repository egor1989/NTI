//
//  ServerCommunication.h
//  NTI
//
//  Created by Елена on 29.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface ServerCommunication : NSObject {
    NSString *returnString; 
    NSMutableURLRequest *request;
    NSData *requestData;
    
}
- (NSString *)regUser:(NSString *)login password:(NSString *)password email:(NSString *)email;
- (void)uploadData:(NSString *)fileContent;
//- (void)checkErrors:(NSString *)answerString;

@end
