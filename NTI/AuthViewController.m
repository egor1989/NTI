//
//  AuthViewController.m
//  NTI
//
//  Created by Елена on 07.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "AuthViewController.h"

@implementation AuthViewController


- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
        [loginField becomeFirstResponder];
    }
    return self;
}

- (void)didReceiveMemoryWarning
{
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

#pragma mark - View lifecycle

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
}

- (void)viewDidUnload
{

    loginField = nil;
    passwordField = nil;
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    // Return YES for supported orientations
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

- (IBAction)cancelButton:(id)sender {
      [self dismissModalViewControllerAnimated:YES];
}

- (IBAction)goButton:(id)sender {
    ServerCommunication *serverCommunication = [[ServerCommunication alloc] init];
    if (![serverCommunication checkInternetConnection]) {
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Ошибка!" message:@"Отсутствует Интернет-соединение. Включите Интернет и повторите попытку" delegate:self cancelButtonTitle:@"ОК" otherButtonTitles:nil];
        [alert show];
    }
    
    else if ([self checkData]) {
        NSData *password = [passwordField.text dataUsingEncoding:NSUTF8StringEncoding];
        EncryptionData *encryptionData = [[EncryptionData alloc] init];
        NSString *encryptedPass = [encryptionData encryptionPassword:password];
        //  NSLog(@"%@", encryptedPass);
        
        // отправка на сервер
        
        [serverCommunication authUser:loginField.text secret:encryptedPass];
        [serverCommunication showResult];
     /*   NSLog(@"auth %@", serverAnswer);
        BOOL ok = [serverCommunication checkErrors: serverAnswer];
        if (ok) {
            NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
            [userDefaults setValue: loginField.text forKey:@"login"];
            [userDefaults setValue: passwordField.text forKey:@"password"];
            [userDefaults synchronize];
        }
      */
        
    }

    
    
}

- (BOOL)checkData{
    if (![loginField.text isEqual:@""]&&![passwordField.text isEqual:@""]) return YES;
    else {
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Ошибка!" message:@"заполните все поля" delegate:self cancelButtonTitle:@"ОК" otherButtonTitles:nil];
        [alert show];
        return NO;  
    }
}

- (void)showResult: (NSString *)answer{
    
    
}
@end
