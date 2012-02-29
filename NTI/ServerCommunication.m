//
//  ServerCommunication.m
//  NTI
//
//  Created by Елена on 29.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "ServerCommunication.h"

@implementation ServerCommunication


- (void)uploadData:(NSString *)fileContent{
    

    fileContent=[@"data={\"method\":\"addNTIFile\",\"params\":{\"ntifile\":" stringByAppendingString:fileContent];
    fileContent=[fileContent stringByAppendingString:@"}}"];
    
    NSLog(@"Request: %@", fileContent);
        
   NSMutableURLRequest *request = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:@"http://goodroads.ru/another/api.php"]cachePolicy:NSURLRequestUseProtocolCachePolicy
                                  timeoutInterval:60.0];

    NSData *requestData = [NSData dataWithBytes:[fileContent UTF8String] length:[fileContent length]];
    [request setHTTPMethod:@"POST"];
    [request setHTTPBody: requestData];
//    [request setAllHTTPHeaderFields:headers];
    
    //send without answer [NSURLConnection connectionWithRequest:request delegate: self];

    
    [NSURLConnection sendAsynchronousRequest:request
                                       queue:[NSOperationQueue mainQueue]
                           completionHandler:^(NSURLResponse *response, NSData *responseData, NSError *error) {
                               returnString = [[NSString alloc] initWithData:responseData encoding: NSUTF8StringEncoding];
                               NSLog(@"returnData: %@", returnString);
                           }];
   // [self checkErrors:returnString];
}

/*- (void)checkErrors:(NSString *)answerString{
    
    SBJsonParser *jsonParser = [SBJsonParser new];
    NSArray *answer = [jsonParser objectWithString:answerString error:NULL];
    NSArray *error = [answer valueForKey:@"error"];
    NSInteger code =[[error valueForKey:@"code"] intValue];
    
    if (code == 80) { 
        NSLog(@"User isn't logged in");
    }
    else if ((code == 81)||(code == 82)){
        NSLog(@"Key incorrect");
        NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
        NSString *login = [userDefaults stringForKey:@"login"];
        NSString *password = [userDefaults stringForKey:@"password"];
        ServerCommunication *serverCommunication = [[[ServerCommunication alloc] init:NULL] autorelease];
        [serverCommunication sendData:login secret: password];
    }
    
}
 */

@end
