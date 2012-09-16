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

- (void)uploadData:(NSData *)fileContent{
    
    NSLog(@"SC -upload data");
    NSString *cookie = [self refreshCookie]; 
    NSLog(@"current cookie = %@",cookie);
    
    NSString *sJSON = [[NSString alloc] initWithData:fileContent encoding:NSASCIIStringEncoding]; 

    NSString *requestContent = [NSString stringWithFormat:@"{\"method\":\"addNTIFile\",\"params\":{\"ntifile\":%@}}",sJSON];
        
    NSLog(@"Request: %@", requestContent);
        
   request = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:@"http://nti.goodroads.ru/api/"]cachePolicy:NSURLRequestUseProtocolCachePolicy
                                  timeoutInterval:60.0];

    requestData = [NSData dataWithBytes:[requestContent UTF8String] length:[requestContent length]];
    NSData *compressData = [GzipCompress gzipDeflate:requestData];

 //   NSLog(@"cData = %@", compressData);
        
    NSString* requestDataFull = [NSString stringWithFormat:@"data=%@%@",[compressData description],@"&zip=1"];
   // NSLog(@"%@", requestDataFull);
    
    [request setHTTPMethod:@"POST"];
    [request setHTTPBody: [NSData dataWithBytes:[requestDataFull UTF8String] length:[requestDataFull length]]];    
    [request setValue:@"gzip" forHTTPHeaderField:@"Content-Encoding"];
    [request setValue:@"gzip" forHTTPHeaderField:@"Accept-Encoding"];
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
    
    NSError *requestError = nil;
    NSURLResponse *response = nil;
    NSData *returnData = [NSURLConnection sendSynchronousRequest: request returningResponse: &response error: &requestError ];
    
    if (requestError!=nil) {
        NSLog(@"%@", requestError);
    }
    
    returnString = [[NSString alloc] initWithData:returnData encoding: NSUTF8StringEncoding];
    NSLog(@"returnData: %@", returnString);
    [self checkErrors:returnString method:@"sendData"];
}
 
 
- (BOOL)checkCookieExpires{
    NSLog(@"check cookie expires");
    //текущая дата в нужном формате
    NSDate * now = [NSDate date];
    NSDateFormatter * date_format = [[NSDateFormatter alloc] init];
    [date_format setLocale:[[NSLocale alloc] initWithLocaleIdentifier:@"en_US"]];
    [date_format setTimeZone:[NSTimeZone timeZoneWithAbbreviation:@"GMT"]];
    
    [date_format setDateFormat: @"EEE, dd-MMM-yyyy HH:mm:ss"]; //Wed, 28-Mar-2012 12:05:35
    //NSString * date_string = [date_format stringFromDate: now];
    //NSLog (@"Date: %@", date_string);
    
    //NSLog(@"now = %@", now);
    //берем дату из текущих cookie
    NSLog(@"cookie = %@",[[NSUserDefaults standardUserDefaults] objectForKey:@"cookieWithDate"]);
    NSString *cookieDate = [self getStringBetweenStrings:[[NSUserDefaults standardUserDefaults] objectForKey:@"cookieWithDate"] first:@"expires=" second:@"GMT"];
    NSLog(@"cookie date = %@",cookieDate);
    
    NSDate * resultD = [date_format dateFromString: cookieDate]; 
    NSLog (@"%@", resultD); 
    
  //  NSString *nowTest = @"2012-05-16 12:50:12";
  //  NSDate * nowT = [date_format dateFromString: nowTest]; 

    
    NSComparisonResult comparetionResult = [now compare:resultD];
    if (comparetionResult == NSOrderedAscending) {
        NSLog(@"now less than resultD");    
        return NO;
    }
   // if (comparetionResult == NSOrderedDescending) NSLog (@"less");

    return YES;
}

- (NSString *) refreshCookie{
    NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
    NSLog(@"refreshCookie");
    if ([self checkCookieExpires]){

        //if ([userDefaults objectForKey:@"cookie"]!=nil) {
            [self authUser:[userDefaults objectForKey:@"login"] secret:[userDefaults objectForKey:@"password"]];
        NSLog(@"reAuth");
        //}
    }
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



- (BOOL)checkErrors:(NSString *)answerString method:(NSString *)methodName{
    NSLog(@"SC check errors");
    if ([answerString isEqual: @""] ) {
        info = @"Пустой ответ";
        UIAlertView* alertView = [[UIAlertView alloc] initWithTitle:info message:@"Данных по поездке за указанный период не существует." delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
        [alertView show];
        errors = YES;
        return errors;
    }
    SBJsonParser *jsonParser = [SBJsonParser new];
    NSArray *answer = [jsonParser objectWithString:answerString error:NULL];
    NSArray *error = [answer valueForKey:@"error"];
    NSInteger code =[[error valueForKey:@"code"] intValue];
    NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
    info = nil;

    errors = YES;
    NSLog(@"code = %i", code);
    

    switch (code) {
        case 0:
            if ([methodName isEqualToString: @"reg&auth"]){
                info = @"Поздравляем!";
                [userDefaults removeObjectForKey:@"cookie"];
                [userDefaults setValue:[answer valueForKey:@"result"] forKey:@"cookie"];
                NSLog(@"auth or reg - OK");
                [userDefaults synchronize];
            }
            if ([methodName isEqualToString: @"sendData"]){
                info = @"Данные успешно отправлены";
                NSLog(@"send - OK");
                
            }
            
            //для получения данных
            if ([methodName isEqualToString: @"password"]){
                info = @"Инструкции высланы на почту, введенную при регистрации";
                NSLog(@"send - OK");
                
            }
            
            errors = NO;
            break;
            
        case 1:
            info = @"Не удалось получить данные";
            break;
            
        case 2:
        case 3:
        case 4:
        case 32:
            info = @"Неверный формат данных. Сообщите разработчикам";
            break;
            
        case 5:
        case 6:
            info = @"Ошибка в запросе. Сообщите разработчикам";
            break;
            
        case 7:
            info = @"Ошибка подключения к БД";
            break;
            
        case 11:
            info = @"Не все обязательные поля заполнены";
            break;
            
        case 12:
            info = @"Пользователь с таким именем уже существует";
            break; 
            
        case 13:
            info = @"E-mail уже используется";
            break;    
        
        case 14:
        case 21:
            info = @"Повторите ввод пароля";
            break; 
            
        case 15:
            info = @"Поле должно быть меньше 32 символов";
            break;
        case 16:
            info = @"Поле email слишком короткое";
            break;  
            
        case 22:
            info = @"Пользователя с таким именем не существует";
            break;
        case 23:
            info = @"Неверный логин и/или пароль";
            break;
            
        case 31:
        case 41: 
        case 52:
        case 71:{
            info = @"Ошибка авторизации";
            NSLog(@"AUTH FAILED!!!");
            [self refreshCookie];
            break;
        }
        case 33:
            info = @"Файл пуст";
            break;
        
        case 51:{
            info = @"Нет данных для пользователя";
            UIAlertView* alertView = [[UIAlertView alloc] initWithTitle:info message:@"Данных по поездке за указанный период не существует. Пожалуйста выберите другую дату" delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
            NSLog(@"bad data");
            [alertView show];
            break;
        }
            
        case 61:
            info = @"Передаваемые данные слишком короткие";
            break;
        
            
        case 62:{
            info = @"Невозможно восстановить пароль для пользователя";
            NSLog(@"can't restore password");
            break;
        }
            
        case 88: {
           NSLog(@"server unrechable");
            info = @"Сервер временно не доступен";
            break;
        }
            
        default:{
            
            info = [NSString stringWithFormat:@"Ошибка. Код = %i", code];//@"Ошибка";
            NSLog(@"ERROR code %i", code);
            break;
        }
    }

    return errors;
}


- (void)showResult{
   
        NSLog(@"alert - server answer");
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Ответ сервера" message:info delegate:self cancelButtonTitle:@"ОК" otherButtonTitles:nil];
        [alert show];    
    
}


+ (void)sendNotification: (NSString *)time lng:(NSString *)longitude lat:(NSString *)latitude{
    
    NSString *cookie = [[NSUserDefaults standardUserDefaults] valueForKey:@"cookie"]; 
    
    NSString *data = [NSString stringWithFormat:(@"data={\"method\":\"deadMoving\",\"params\":{\"time\":\"%@\",\"lng\":\"%@\",\"lat\":\"%@\"}}"),time,longitude,latitude];
    NSLog(@"Request: %@", data);
    
    NSMutableURLRequest *request = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:@"http://nti.goodroads.ru/api/"]cachePolicy:NSURLRequestUseProtocolCachePolicy
                                  timeoutInterval:60.0];
    
    NSData *requestData = [NSData dataWithBytes:[data UTF8String] length:[data length]];
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
    
    
    NSError *requestError = nil;
    NSURLResponse *response = nil;
    NSData *returnData = [NSURLConnection sendSynchronousRequest: request returningResponse: &response error: &requestError ];
    
    if (requestError!=nil) {
        NSLog(@"%@", requestError);
    }
    
    NSString *returnString = [[NSString alloc] initWithData:returnData encoding: NSUTF8StringEncoding];
    NSLog(@"returnData: %@", returnString);
    //[self checkErrors:returnString method:@"stat"];
}



+ (void)sendAliveInfo{
    
    NSString *cookie = [[NSUserDefaults standardUserDefaults] valueForKey:@"cookie"]; 
    NSString *data = @"data={\"method\":\"keepAlive\"}";
    NSLog(@"Request: %@", data);
    
    NSMutableURLRequest *request = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:@"http://nti.goodroads.ru/api/"]cachePolicy:NSURLRequestUseProtocolCachePolicy
                                                       timeoutInterval:60.0];
    
    NSData *requestData = [NSData dataWithBytes:[data UTF8String] length:[data length]];
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
    
    
    NSError *requestError = nil;
    NSURLResponse *response = nil;
    NSData *returnData = [NSURLConnection sendSynchronousRequest: request returningResponse: &response error: &requestError ];
    
    if (requestError!=nil) {
        NSLog(@"%@", requestError);
    }
    
    NSString *returnString = [[NSString alloc] initWithData:returnData encoding: NSUTF8StringEncoding];
    NSLog(@"returnData: %@", returnString);
    //[self checkErrors:returnString method:@"stat"];
}


- (NSString *)getAllStatistic{
    
    NSString *cookie = [[NSUserDefaults standardUserDefaults] valueForKey:@"cookie"]; 

    NSString *data = @"data={\"method\":\"getStatistics\"}";
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
    

    NSError *requestError = nil;
    NSURLResponse *response = nil;
    NSData *returnData = [NSURLConnection sendSynchronousRequest: request returningResponse: &response error: &requestError ];
    
    if (requestError!=nil) {
        NSLog(@"%@", requestError);
    }
    
    returnString = [[NSString alloc] initWithData:returnData encoding: NSUTF8StringEncoding];
    NSLog(@"returnData: %@", returnString);
    [self checkErrors:returnString method:@"stat"];
    if (!errors) return returnString; 
    else return @"error";
}


- (NSString *)getLastStatistic{
   NSString *cookie = [[NSUserDefaults standardUserDefaults] valueForKey:@"cookie"]; 
    NSLog(@"cookie = %@", cookie);

    NSString *data = @"data={\"method\":\"getStatistics\",\"params\":{\"last\":\"1\"}}";
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
    
    NSError *requestError = nil;
    NSURLResponse *response = nil;
    NSData *returnData = [NSURLConnection sendSynchronousRequest: request returningResponse: &response error: &requestError ];
    
    if (requestError!=nil) {
        NSLog(@"%@", requestError);
    }
    
    returnString = [[NSString alloc] initWithData:returnData encoding: NSUTF8StringEncoding];
    NSLog(@"returnData: %@", returnString);
    [self checkErrors:returnString method:@"stat"];
    if (!errors) return returnString; 
    else return @"error";
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
    }
    
    returnString = [[NSString alloc] initWithData:returnData encoding: NSUTF8StringEncoding];
    NSLog(@"returnData: %@", returnString);
    [self checkErrors:returnString method:@"reg&auth"];
    
    if (!errors) {
        NSDictionary *fields = [(NSHTTPURLResponse *)response allHeaderFields];
        NSString *cookieWithDate = [fields valueForKey:@"Set-Cookie"];
      //  NSLog(@"Cookie: %@", cookie);
        NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
        [userDefaults setValue: login forKey:@"login"];
        [userDefaults setValue: password forKey:@"password"];
        [userDefaults setValue: cookieWithDate forKey:@"cookieWithDate"];
        [userDefaults synchronize];
        info = @"Поздравляем! Регистрация прошла успешно";
        
        //на таб
    }
    [self showResult];
    
    
}


- (void) authUser:(NSString *)login secret:(NSString *)message{
    [self infoAboutDevice];
    
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
    }
    returnString = [[NSString alloc] initWithData:returnData encoding: NSUTF8StringEncoding];
    NSLog(@"returnData: %@", returnString);
    [self checkErrors: returnString method:@"reg&auth"];
    if (!errors) {

        NSDictionary *fields = [(NSHTTPURLResponse *)response allHeaderFields];
        NSString *cookieWithDate = [fields valueForKey:@"Set-Cookie"];
       // NSLog(@"Cookie: %@", cookie);
        NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
        [userDefaults setValue: login forKey:@"login"];
        [userDefaults setValue: message forKey:@"password"];
        [userDefaults setValue: cookieWithDate forKey:@"cookieWithDate"];
        [userDefaults synchronize];
        NSLog(@"cookie - %@", [userDefaults valueForKey:@"cookie"]);
        info = @"Поздравляем! Авторизация прошла успешно";
        
        //на таб
    }

    
}

+ (BOOL)checkInternetConnection: (BOOL)needNotify{

    Reachability* reach = [Reachability reachabilityForInternetConnection];
    
    NetworkStatus hostStatus = [reach currentReachabilityStatus];
    NSLog(@"internetUserPreference = %@", [[NSUserDefaults standardUserDefaults] boolForKey:@"internetUserPreference"]?@"YES":@"NO");
    if (![[NSUserDefaults standardUserDefaults] boolForKey:@"internetUserPreference"]) {
        if (hostStatus == NotReachable){
            NSLog(@"internet: -");
            if (needNotify) {
                UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Ошибка!" message:@"Включите Интернет-соединение и повторите попытку" delegate:self cancelButtonTitle:@"ОК" otherButtonTitles:nil];
                [alert show];
            }
            return NO;
        } 
        else return YES;
    }
    else if (hostStatus == ReachableViaWiFi){
        NSLog(@"internet: wi-fi");
        return YES;
        } 
        else{
            NSLog(@"internet: -");
            if (needNotify) {
                UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Ошибка!" message:@"Включите Интернет-соединение и повторите попытку" delegate:self cancelButtonTitle:@"ОК" otherButtonTitles:nil];
                [alert show];
            }
            return NO;

        }
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
    NSLog(@"Get Route from server");
    NSString *cookie = [self refreshCookie]; 
    NSLog(@"cookie = %@", cookie);
    NSString *timeString;
    if (timeInterval == 0) {
         timeString = @"data={\"method\":\"getPath\"}";
    }
    else{
        timeString = [NSString stringWithFormat:@"%.0f",timeInterval];
        timeString=[@"data={\"method\":\"getPath\",\"params\":{\"day\":1, \"time\":" stringByAppendingString:timeString];
        timeString=[timeString stringByAppendingString:@"}}"];
    }
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
    NSHTTPCookie *fcookie = [NSHTTPCookie cookieWithProperties:properties]; 
    NSArray* fcookies = [NSArray arrayWithObjects: fcookie, nil];   
    NSDictionary *headers = [NSHTTPCookie requestHeaderFieldsWithCookies:fcookies]; 
    
    [request setAllHTTPHeaderFields:headers];
    
    [NSURLConnection sendAsynchronousRequest:request
                                       queue:[NSOperationQueue mainQueue]
                           completionHandler:^(NSURLResponse *response, NSData *responseData, NSError *error) {
                              
                               NSMutableData *resData = [responseData mutableCopy];
                               [resData replaceBytesInRange:NSMakeRange(0, 3) withBytes:NULL length:0];
                               NSLog(@"compressedDAta= %@", resData);
                               
                               NSData *unCompressData = [[NSData alloc] init];
                               unCompressData = [GzipCompress gzipInflate:resData];
                               returnString = [[NSString alloc] initWithData:unCompressData encoding: NSUTF8StringEncoding];
                               NSLog(@"returnData: %@", returnString);
                               if (![self checkErrors:returnString method:@"getRouteFromServer"]) {
                                   [[NSNotificationCenter defaultCenter]	postNotificationName:	@"routePointsReceived" object:  returnString];
                               }
                               else{
                                   [[NSNotificationCenter defaultCenter]	postNotificationName:	@"routePointsReceivedWithError" object:  nil];
                               }
                               }];
}

- (void)sendFeedBackToServerWithTitle:(NSString*)title andBody: (NSString*)body{
    NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
    NSLog(@"cookie = %@", [userDefaults valueForKey:@"cookie"]);
    NSString *cookie = [userDefaults valueForKey:@"cookie"];
    
    NSString* dataString = [NSString stringWithFormat:@"data={\"method\":\"feedBack\",\"params\":{\"title\":\"%@\",\"body\":\"%@\"", title, body];
    dataString=[dataString stringByAppendingString:@"}}"];
    
    NSLog(@"Request: %@", dataString);
    
    request = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:@"http://nti.goodroads.ru/api/"]cachePolicy:NSURLRequestUseProtocolCachePolicy
                                  timeoutInterval:60.0];
    
    requestData = [NSData dataWithBytes:[dataString UTF8String] length:[dataString length]];
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
                               [[NSNotificationCenter defaultCenter]	postNotificationName:	@"feedBackSend" object:  returnString];
                               
                           }];
    
}

- (void)sendInterviewToServerWithData:(NSDictionary*)data{
    NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
    NSLog(@"cookie = %@", [userDefaults valueForKey:@"cookie"]);
    NSString *cookie = [userDefaults valueForKey:@"cookie"];
    
    NSDictionary *allDataDict = [[NSDictionary alloc]initWithObjectsAndKeys:
                                @"addQuest", @"method",
                                data, @"params", 
                                nil];
    NSString *dataSting = [allDataDict JSONRepresentation];
//    NSString *finalDataString = @"data={\"method\":\"addQuest\",\"params\":{\"age\":\"541\",\"autotype\":\"A\",\"skill\":\" \",\"dtp\":\"552\",\"company\":\"РосГосСтрах\",\"sex\":\"Мужской\",\"autopower\":\"80-100 л.с.\"}}";
    NSString *finalDataString = [@"data=" stringByAppendingString: dataSting];
    NSLog(@"Request: %@", finalDataString);
    
    request = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:@"http://nti.goodroads.ru/api/"]cachePolicy:NSURLRequestUseProtocolCachePolicy
                                  timeoutInterval:60.0];
    requestData = [NSData dataWithBytes:[finalDataString cStringUsingEncoding:NSUTF8StringEncoding] length:[finalDataString lengthOfBytesUsingEncoding:NSUTF8StringEncoding]];
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
                               
                           }];
    
}

- (void)forgotPassword: (NSString *)login{
    NSString *data = [NSString stringWithFormat: @"data={\"method\":\"rememberPassword\",\"params\":{\"login\":\"%@\"}}", login];
    NSLog(@"Request: %@", data);
    
    request = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:@"http://nti.goodroads.ru/api/"]cachePolicy:NSURLRequestUseProtocolCachePolicy
                                  timeoutInterval:60.0];
    
    requestData = [NSData dataWithBytes:[data UTF8String] length:[data length]];
    [request setHTTPMethod:@"POST"];
    [request setHTTPBody: requestData];    
    
    NSData *returnData = [NSURLConnection sendSynchronousRequest: request returningResponse: nil error: nil];
    

    returnString = [[NSString alloc] initWithData:returnData encoding: NSUTF8StringEncoding];
    NSLog(@"returnData: %@", returnString);
    [self checkErrors:returnString method:@"password"];
    
    
}



@end
