//
//  ServerCommunication.m
//  NTI
//
//  Created by Елена on 29.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "ServerCommunication.h"

@implementation ServerCommunication
@synthesize errors;


- (void)uploadData:(NSString *)fileContent{
    NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
    NSLog(@"cookie = %@", [userDefaults valueForKey:@"cookie"]);
    NSString *cookie = [userDefaults valueForKey:@"cookie"]; 
  //  NSString * cookie = [self refreshCookie];
    
    fileContent=[@"data={\"method\":\"addNTIFile\",\"params\":{\"ntifile\":" stringByAppendingString:fileContent];
    fileContent=[fileContent stringByAppendingString:@"}}"];
    
    NSLog(@"Request: %@", fileContent);
        
   request = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:@"http://nti.goodroads.ru/api/"]cachePolicy:NSURLRequestUseProtocolCachePolicy
                                  timeoutInterval:60.0];

    requestData = [NSData dataWithBytes:[fileContent UTF8String] length:[fileContent length]];
    [request setHTTPMethod:@"POST"];
    [request setHTTPBody: requestData];    
    
     NSDictionary *properties = [NSDictionary dictionaryWithObjectsAndKeys:
     @"http://nti.goodroads.ru/api/", NSHTTPCookieDomain,
                                 @"NTIKeys", NSHTTPCookieName,
     cookie, NSHTTPCookieValue,
     @"/", NSHTTPCookiePath,
     nil];
     
     [[NSHTTPCookieStorage sharedHTTPCookieStorage] setCookie:[NSHTTPCookie cookieWithProperties:properties]];
     NSHTTPCookie *fcookie = [NSHTTPCookie cookieWithProperties:properties]; //?
     NSArray* fcookies = [NSArray arrayWithObjects: fcookie, nil];   //?
     NSDictionary * headers = [NSHTTPCookie requestHeaderFieldsWithCookies:fcookies]; //?
     
     [request setAllHTTPHeaderFields:headers];
     
    [NSURLConnection sendAsynchronousRequest:request
                                       queue:[NSOperationQueue mainQueue]
                           completionHandler:^(NSURLResponse *response, NSData *responseData, NSError *error) {
                               returnString = [[NSString alloc] initWithData:responseData encoding: NSUTF8StringEncoding];
                               NSLog(@"returnData: %@", returnString);
                               //[self checkErrors:returnString];
                               // провверка на ошибки при отправке файла // если нет можно очистить БД
                           }];

}

- (BOOL)checkCookieExpires{
    //текущая дата в нужном формате
    NSDate * now = [NSDate date];
    NSDateFormatter * date_format = [[NSDateFormatter alloc] init];
    [date_format setLocale:[[NSLocale alloc] initWithLocaleIdentifier:@"en_US"]];
    [date_format setTimeZone:[NSTimeZone timeZoneWithAbbreviation:@"GMT"]];
    
    [date_format setDateFormat: @"EEE, dd-MMM-YYYY HH:mm:ss"]; //Wed, 28-Mar-2012 12:05:35
    NSString * date_string = [date_format stringFromDate: now];
    NSLog (@"Date: %@", date_string);
    
    //берем дату из текущих cookie
    NSLog(@"cookie = %@",[[NSUserDefaults standardUserDefaults] objectForKey:@"cookie"]);
    NSString *cookieDate = [self getStringBetweenStrings:[[NSUserDefaults standardUserDefaults] objectForKey:@"cookie"] first:@"expires=" second:@"GMT"];
    NSLog(@"cookie date = %@",cookieDate);
    NSDate * resultD = [date_format dateFromString: cookieDate]; 
    NSLog (@"%@", resultD); 

    return YES;
}

- (NSString *) refreshCookie{
    
    NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
    //if ([self checkCookieExpires]){

        if ([userDefaults objectForKey:@"cookie"]!=nil) {
            [self authUser:[userDefaults objectForKey:@"login"] secret:[userDefaults objectForKey:@"password"]];
        }
    //}
    return [userDefaults objectForKey:@"cookie"];
    
}


- (NSString*) getStringBetweenStrings: (NSString *) main first:(NSString *)first second: (NSString*) second{
	NSRange rangeofFirst = [main rangeOfString:first];
	NSRange rangeOfSecond = [main rangeOfString:second];
	if ((rangeofFirst.length == 0) || (rangeOfSecond.length == 0)) {
		return nil;
	}
	NSString *resultD = [[main substringFromIndex:rangeofFirst.location+rangeofFirst.length] 
						substringToIndex:
						[[main substringFromIndex:rangeofFirst.location+rangeofFirst.length] rangeOfString:second].location];
	return resultD;
}



- (BOOL)checkErrors:(NSString *)answerString{
    
    SBJsonParser *jsonParser = [SBJsonParser new];
    NSArray *answer = [jsonParser objectWithString:answerString error:NULL];
    NSArray *error = [answer valueForKey:@"error"];
    NSInteger code =[[error valueForKey:@"code"] intValue];
    NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
    info = nil;
    forgotPassword = NO;
    errors = YES;
    
    switch (code) {
        case 0:
            info = @"Поздравляем!";
            [userDefaults removeObjectForKey:@"cookie"];
            [userDefaults synchronize];
            [userDefaults setValue:[answer valueForKey:@"result"] forKey:@"cookie"];
            errors = NO;
            break;
        case 2:
        case 10:
            info = @"Не все обязательные поля заполнены";
            break;
        case 3:
            info = @"Пользователь с таким именем уже существует";
            break;
        case 4:
            info = @"E-mail уже используется";
            break;
        case 11:
            info = @"Пользователя с таким именем не существует";
            break;
        case 12:
            info = @"Неверный пароль";
            forgotPassword = YES;
            break;
            
        default:
            break;
    }

    return errors;
}


- (void)showResult{
    if (forgotPassword) {
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Ошибка!" message:info delegate:self cancelButtonTitle:@"Еще раз" otherButtonTitles:@"Забыл пароль",nil];
        [alert show];
    }
    else {
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Ответ сервера" message:info delegate:self cancelButtonTitle:@"ОК" otherButtonTitles:nil];
        [alert show];    
    }
    
}


- (void)getAllStatistic{
    
    NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
    NSLog(@"cookie = %@", [userDefaults valueForKey:@"cookie"]);
    NSString *cookie = [userDefaults valueForKey:@"cookie"]; 
    //  NSString * cookie = [self refreshCookie];
    //data={\"method\":\getStatistics\"}
    NSString *data = @"data={\"method\":\"getStatistics\"}";//,\"params\":{\"login\":\"",login, @"\",\"password\":\"", password, @"\"}}"];
    NSLog(@"Request: %@", data);
    
    request = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:@"http://nti.goodroads.ru/api/"]cachePolicy:NSURLRequestUseProtocolCachePolicy
                                  timeoutInterval:60.0];
    
    requestData = [NSData dataWithBytes:[data UTF8String] length:[data length]];
    [request setHTTPMethod:@"POST"];
    [request setHTTPBody: requestData];    
    
    NSDictionary *properties = [NSDictionary dictionaryWithObjectsAndKeys:
                                @"http://nti.goodroads.ru/api/", NSHTTPCookieDomain,
                                @"NTIKeys", NSHTTPCookieName,
                                cookie, NSHTTPCookieValue,
                                @"/", NSHTTPCookiePath,
                                nil];
    
    [[NSHTTPCookieStorage sharedHTTPCookieStorage] setCookie:[NSHTTPCookie cookieWithProperties:properties]];
    NSHTTPCookie *fcookie = [NSHTTPCookie cookieWithProperties:properties]; //?
    NSArray* fcookies = [NSArray arrayWithObjects: fcookie, nil];   //?
    NSDictionary * headers = [NSHTTPCookie requestHeaderFieldsWithCookies:fcookies]; //?
    
    [request setAllHTTPHeaderFields:headers];
    
    
   // NSData *returnData = [NSURLConnection sendSynchronousRequest: request returningResponse: &response error: &requestError ];
   // [NSURLConnection sendAsynchronousRequest:request
   //                                    queue:[NSOperationQueue mainQueue]
   //                        completionHandler:^(NSURLResponse *response, NSData *responseData, NSError *error) {
   //                            returnString = [[NSString alloc] initWithData:responseData encoding: NSUTF8StringEncoding];
   //                            NSLog(@"returnData: %@", returnString);
                               //[self checkErrors:returnString];
                               // провверка на ошибки при получении статистики 
   //                        }];
    NSError *requestError = nil;
    NSURLResponse *response = nil;
    NSData *returnData = [NSURLConnection sendSynchronousRequest: request returningResponse: &response error: &requestError ];
    
    if (requestError!=nil) {
        NSLog(@"%@", requestError);
        NSLog(@"ERROR!ERROR!ERROR!");
    }
    
    returnString = [[NSString alloc] initWithData:returnData encoding: NSUTF8StringEncoding];
    NSLog(@"returnData: %@", returnString);

    
}


- (void)getLastStatistic{
    
    NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
    NSLog(@"cookie = %@", [userDefaults valueForKey:@"cookie"]);
    NSString *cookie = [userDefaults valueForKey:@"cookie"]; 
    //  NSString * cookie = [self refreshCookie];
    //data={\"method\":\getStatistics\"}
    NSString *data = @"data={\"method\":\"getStatistics\",\"params\":{\"last\":\"1\"}}";//,\"params\":{\"login\":\"",login, @"\",\"password\":\"", password, @"\"}}"];
    NSLog(@"Request: %@", data);
    
    request = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:@"http://nti.goodroads.ru/api/"]cachePolicy:NSURLRequestUseProtocolCachePolicy
                                  timeoutInterval:60.0];
    
    requestData = [NSData dataWithBytes:[data UTF8String] length:[data length]];
    [request setHTTPMethod:@"POST"];
    [request setHTTPBody: requestData];    
    
    NSDictionary *properties = [NSDictionary dictionaryWithObjectsAndKeys:
                                @"http://nti.goodroads.ru/api/", NSHTTPCookieDomain,
                                @"NTIKeys", NSHTTPCookieName,
                                cookie, NSHTTPCookieValue,
                                @"/", NSHTTPCookiePath,
                                nil];
    
    [[NSHTTPCookieStorage sharedHTTPCookieStorage] setCookie:[NSHTTPCookie cookieWithProperties:properties]];
    NSHTTPCookie *fcookie = [NSHTTPCookie cookieWithProperties:properties]; //?
    NSArray* fcookies = [NSArray arrayWithObjects: fcookie, nil];   //?
    NSDictionary * headers = [NSHTTPCookie requestHeaderFieldsWithCookies:fcookies]; //?
    
    [request setAllHTTPHeaderFields:headers];
    
    
    // NSData *returnData = [NSURLConnection sendSynchronousRequest: request returningResponse: &response error: &requestError ];
    // [NSURLConnection sendAsynchronousRequest:request
    //                                    queue:[NSOperationQueue mainQueue]
    //                        completionHandler:^(NSURLResponse *response, NSData *responseData, NSError *error) {
    //                            returnString = [[NSString alloc] initWithData:responseData encoding: NSUTF8StringEncoding];
    //                            NSLog(@"returnData: %@", returnString);
    //[self checkErrors:returnString];
    // провверка на ошибки при получении статистики 
    //                        }];
    NSError *requestError = nil;
    NSURLResponse *response = nil;
    NSData *returnData = [NSURLConnection sendSynchronousRequest: request returningResponse: &response error: &requestError ];
    
    if (requestError!=nil) {
        NSLog(@"%@", requestError);
        NSLog(@"ERROR!ERROR!ERROR!");
    }
    
    returnString = [[NSString alloc] initWithData:returnData encoding: NSUTF8StringEncoding];
    NSLog(@"returnData: %@", returnString);
    
    
}


 
 

- (void)regUser:(NSString *)login password:(NSString *)password email:(NSString *)email{
    
    NSLog(@"sendData login = %@ message = %@ email = %@", login, password, email);
    
    NSString *data = [NSString stringWithFormat:(@"%@%@%@%@%@"),@"data={\"method\":\"NTIregister\",\"params\":{\"login\":\"",login, @"\",\"password\":\"", password, @"\"}}"];
    NSLog(@"Request: %@", data);
    request = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:@"http://nti.goodroads.ru/api/"]cachePolicy:NSURLRequestUseProtocolCachePolicy
                                  timeoutInterval:60.0];
    
    requestData = [NSData dataWithBytes:[data UTF8String] length:[data length]];
    [request setHTTPMethod:@"POST"];
    [request setHTTPBody: requestData];
    
    
    NSError *requestError = nil;
    NSURLResponse *response = nil;
    NSData *returnData = [NSURLConnection sendSynchronousRequest: request returningResponse: &response error: &requestError ];
    
    if (requestError!=nil) {
        NSLog(@"%@", requestError);
        NSLog(@"ERROR!ERROR!ERROR!");
    }
    
    returnString = [[NSString alloc] initWithData:returnData encoding: NSUTF8StringEncoding];
    NSLog(@"returnData: %@", returnString);
    [self checkErrors:returnString];
    
    if (!errors) {
      //  NSDictionary *fields = [(NSHTTPURLResponse *)response allHeaderFields];
      //  NSString *cookie = [fields valueForKey:@"Set-Cookie"];
      //  NSLog(@"Cookie: %@", cookie);
        NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
        [userDefaults setValue: login forKey:@"login"];
        [userDefaults setValue: password forKey:@"password"];
      //  [userDefaults setValue: result forKey:@"cookie"];
      ///  [userDefaults synchronize];
        info = @"Поздравляем! Регистрация прошла успешно";
        
        //на таб
    }
    [self showResult];
    
    
}


- (void) authUser:(NSString *)login secret:(NSString *)message{
    [self infoAboutDevice];
    
    
    //device, model, version, service
    
    
    NSString *data = [NSString stringWithFormat:(@"data={\"method\":\"NTIauth\",\"params\":{\"login\":\"%@%@%@%@%@%@%@%@%@%@%@%@"),login, @"\",\"secret\":\"", message,@"\",\"device\":\"", deviceName,@"\",\"model\":\"", model,@"\",\"version\":\"", systemVersion, @"\",\"carrier\":\"", carrierName, @"\"}}"];
    
    NSLog(@"Request: %@", data);
    
    request = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:@"http://nti.goodroads.ru/api/"]cachePolicy:NSURLRequestUseProtocolCachePolicy
                                  timeoutInterval:60.0];
    
    requestData = [NSData dataWithBytes:[data UTF8String] length:[data length]];
    [request setHTTPMethod:@"POST"];
    [request setHTTPBody: requestData];

    NSError *requestError = nil;
    NSURLResponse *response = nil;
    NSData *returnData = [NSURLConnection sendSynchronousRequest: request returningResponse: &response error: &requestError];
    
    if (requestError!=nil) {
        NSLog(@"%@", requestError);
        NSLog(@"ERROR!ERROR!ERROR!");
    }
    returnString = [[NSString alloc] initWithData:returnData encoding: NSUTF8StringEncoding];
    NSLog(@"returnData: %@", returnString);
    [self checkErrors: returnString];
    if (!errors) {

       // NSDictionary *fields = [(NSHTTPURLResponse *)response allHeaderFields];
       // NSString *cookie = [fields valueForKey:@"Set-Cookie"];
       // NSLog(@"Cookie: %@", cookie);
        NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
        [userDefaults setValue: login forKey:@"login"];
        [userDefaults setValue: message forKey:@"password"];
        NSLog(@"cookie - %@", [userDefaults valueForKey:@"cookie"]);
        
        info = @"Поздравляем! Авторизация прошла успешно";
        
        //на таб
    }

    
}

- (BOOL) checkInternetConnection{
    Reachability* reach = [Reachability reachabilityWithHostname:@"www.goodroads.ru"];

    NetworkStatus hostStatus = [reach currentReachabilityStatus];
    
    if (hostStatus == NotReachable){
        NSLog(@"internet: -");
        return NO;
    } 
    else return YES;
}

- (void) infoAboutDevice{

    deviceName = [[UIDevice currentDevice] name];
    //systemVersion = [[[UIDevice currentDevice] systemName] stringByAppendingFormat: [[UIDevice currentDevice] systemVersion], nil];
    systemVersion = [[UIDevice currentDevice] systemVersion];
    model = [[UIDevice currentDevice] model];
    
    
    // Setup the Network Info and create a CTCarrier object
    CTTelephonyNetworkInfo *networkInfo = [[CTTelephonyNetworkInfo alloc] init] ;
    CTCarrier *carrier = [networkInfo subscriberCellularProvider];
    
    // Get carrier name
    carrierName = [carrier carrierName];
    
    NSLog(@"%@; %@; %@; %@", deviceName, model, systemVersion, carrierName);
    
}

- (void)getRouteFromServer:(float)timeInterval{
    NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
    NSLog(@"cookie = %@", [userDefaults valueForKey:@"cookie"]);
    NSString *cookie = [userDefaults valueForKey:@"cookie"];
    
    NSString *timeString = [NSString stringWithFormat:@"%.0f",timeInterval];
    timeString=[@"data={\"method\":\"getPath\",\"params\":{\"time\":" stringByAppendingString:timeString];
    timeString=[timeString stringByAppendingString:@"}}"];
    
    NSLog(@"Request: %@", timeString);
    
    request = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:@"http://nti.goodroads.ru/api/"]cachePolicy:NSURLRequestUseProtocolCachePolicy
                                  timeoutInterval:60.0];
    
    requestData = [NSData dataWithBytes:[timeString UTF8String] length:[timeString length]];
    [request setHTTPMethod:@"POST"];
    [request setHTTPBody: requestData];    
    
    NSDictionary *properties = [NSDictionary dictionaryWithObjectsAndKeys:
                                @"http://nti.goodroads.ru/api/", NSHTTPCookieDomain,
                                @"NTIKeys", NSHTTPCookieName,
                                cookie, NSHTTPCookieValue,
                                @"/", NSHTTPCookiePath,
                                nil];    
    [[NSHTTPCookieStorage sharedHTTPCookieStorage] setCookie:[NSHTTPCookie cookieWithProperties:properties]];
    NSHTTPCookie *fcookie = [NSHTTPCookie cookieWithProperties:properties]; //?
    NSArray* fcookies = [NSArray arrayWithObjects: fcookie, nil];   //?
    NSDictionary *headers = [NSHTTPCookie requestHeaderFieldsWithCookies:fcookies]; //?
    
    [request setAllHTTPHeaderFields:headers];
    
    [NSURLConnection sendAsynchronousRequest:request
                                       queue:[NSOperationQueue mainQueue]
                           completionHandler:^(NSURLResponse *response, NSData *responseData, NSError *error) {
                               returnString = [[NSString alloc] initWithData:responseData encoding: NSUTF8StringEncoding];
                               NSLog(@"returnData: %@", returnString);
                               [[NSNotificationCenter defaultCenter]	postNotificationName:	@"routePointsReceived" object:  returnString];

//                               [self checkErrors:returnString];
                               //написсать свой checkError
                           }];
    
}





@end
