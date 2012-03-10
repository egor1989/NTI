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
    

    fileContent=[@"data={\"method\":\"addNTIFile\",\"params\":{\"ntifile\":" stringByAppendingString:fileContent];
    fileContent=[fileContent stringByAppendingString:@"}}"];
    
    NSLog(@"Request: %@", fileContent);
        
   request = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:@"http://nti.goodroads.ru/api/"]cachePolicy:NSURLRequestUseProtocolCachePolicy
                                  timeoutInterval:60.0];

    requestData = [NSData dataWithBytes:[fileContent UTF8String] length:[fileContent length]];
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

- (BOOL)checkErrors:(NSString *)answerString{
    
    SBJsonParser *jsonParser = [SBJsonParser new];
    NSArray *answer = [jsonParser objectWithString:answerString error:NULL];
    NSArray *error = [answer valueForKey:@"error"];
    NSInteger code =[[error valueForKey:@"code"] intValue];
    
    NSString *info = nil;
    forgotPassword = NO;
    errors = YES;
    
    switch (code) {
        case 0:
            info = @"Поздравляем!";
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

    [self showResult:info];
    return errors;
}

- (void)showResult: (NSString *)info {
    if (forgotPassword) {
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Ошибка!" message:info delegate:self cancelButtonTitle:@"Еще раз" otherButtonTitles:@"Забыл пароль",nil];
        [alert show];
    }
    else {
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Ответ сервера" message:info delegate:self cancelButtonTitle:@"ОК" otherButtonTitles:nil];
        [alert show];    
    }
    
}
 
 

- (NSString *)regUser:(NSString *)login password:(NSString *)password email:(NSString *)email{
    
    NSLog(@"sendData login = %@ message = %@ email = %@", login, password, email);
    
    NSString *data = [NSString stringWithFormat:(@"%@%@%@%@%@%@%@"),@"data={\"method\":\"NTIregister\",\"params\":{\"login\":\"",login, @"\",\"password\":\"", password,@"\",\"email\":\"",email, @"\"}}"];
    NSLog(@"Request: %@", data);
    request = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:@"http://nti.goodroads.ru/api/"]cachePolicy:NSURLRequestUseProtocolCachePolicy
                                  timeoutInterval:60.0];
    
    requestData = [NSData dataWithBytes:[data UTF8String] length:[data length]];
    [request setHTTPMethod:@"POST"];
    [request setHTTPBody: requestData];
    
    NSData *returnData = [NSURLConnection sendSynchronousRequest: request returningResponse: nil error: nil ];
    returnString = [[NSString alloc] initWithData:returnData encoding: NSUTF8StringEncoding];
    NSLog(@"returnData: %@", returnString);
    
    
    
    return returnString;
}


- (NSString *) authUser:(NSString *)login secret:(NSString *)message{
    NSError *requestError = nil;
    // NSLog(@"sendData login = %@ message = %@", login, message);
    
    NSString *data = [NSString stringWithFormat:(@"%@%@%@%@%@"),@"data={\"method\":\"NTIauth\",\"params\":{\"login\":\"",login, @"\",\"secret\":\"", message,@"\"}}"];
    
    NSLog(@"Request: %@", data);
    
    request = [NSMutableURLRequest requestWithURL:[NSURL URLWithString:@"http://nti.goodroads.ru/api/"]cachePolicy:NSURLRequestUseProtocolCachePolicy
                                  timeoutInterval:60.0];
    
    requestData = [NSData dataWithBytes:[data UTF8String] length:[data length]];
    [request setHTTPMethod:@"POST"];
    [request setHTTPBody: requestData];

    
    NSURLResponse *response = nil;
    NSData *returnData = [NSURLConnection sendSynchronousRequest: request returningResponse: &response error: &requestError];
    
    if (requestError!=nil) {
        NSLog(@"ERROR!ERROR!ERROR!");
    }
    
    NSDictionary *fields = [(NSHTTPURLResponse *)response allHeaderFields];
    NSString *cookie = [fields valueForKey:@"Set-Cookie"];
    
    NSLog(@"Cookie: %@", cookie);
    
    returnString = [[NSString alloc] initWithData:returnData encoding: NSUTF8StringEncoding];
    NSLog(@"returnData: %@", returnString);
    return returnString;
    
    [self checkErrors:returnString];
    //[self parseAuthJSON:returnString cookie: cookie method:@"sendData"];
    //parse json
    //return serverAnswer;
}



@end
