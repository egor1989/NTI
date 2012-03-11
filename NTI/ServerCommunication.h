//
//  ServerCommunication.h
//  NTI
//
//  Created by Елена on 29.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "SBJson.h"

@interface ServerCommunication : NSObject {
    NSString *returnString; 
    NSMutableURLRequest *request;
    NSData *requestData;
    BOOL forgotPassword;
    BOOL errors;
    
}
- (NSString *)regUser:(NSString *)login password:(NSString *)password email:(NSString *)email;
- (void)uploadData:(NSString *)fileContent;
- (NSString *)authUser:(NSString *)login secret:(NSString *)message;
- (BOOL)checkErrors:(NSString *)answerString;
- (void)showResult: (NSString *)info;

@property (nonatomic) BOOL errors;

@end
