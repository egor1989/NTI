//
//  ServerCommunication.h
//  NTI
//
//  Created by Елена on 29.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "SBJson.h"
#import "Reachability.h"
#import <CoreTelephony/CTCarrier.h>
#import <CoreTelephony/CTTelephonyNetworkInfo.h>
#import "GzipCompress.h"
#import "toJSON.h"

@interface ServerCommunication : NSObject {
    NSString *returnString; 
    NSMutableURLRequest *request;
    NSData *requestData;
    BOOL errors;
    NSString *info;
    NSString *deviceName;
    NSString *systemVersion;
    NSString *model;
    NSString *carrierName;
    

    
}
- (void)regUser:(NSString *)login password:(NSString *)password email:(NSString *)email;
- (void)uploadData:(NSData *)fileContent;
- (void)authUser:(NSString *)login secret:(NSString *)message;
- (BOOL)checkErrors:(NSString *)answerString method:(NSString *)methodName;
- (void)showResult;
- (NSString *)getStatAll: (BOOL)value;
- (NSString *) refreshCookie;
- (BOOL)checkCookieExpires;
- (NSString*) getStringBetweenStrings: (NSString *) main first:(NSString *)first second: (NSString*) second;
- (void)infoAboutDevice;
- (void)getRouteFromServer:(float)timeInterval;
- (void)sendFeedBackToServerWithTitle:(NSString*)title andBody: (NSString*)body;
- (void)sendInterviewToServerWithData:(NSDictionary*)data;
- (void)forgotPassword: (NSString *)login;

+ (BOOL)checkInternetConnection: (BOOL)needNotify;
+ (void)sendNotification: (NSString *)time lng:(NSString *)longitude lat:(NSString *)latitude;
+ (void)sendAppInfo: (NSArray *)infoArray;


@property (nonatomic) BOOL errors;

@end
