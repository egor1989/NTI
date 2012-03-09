//
//  RegistrationViewController.m
//  NTI
//
//  Created by Елена on 07.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "RegistrationViewController.h"

@implementation RegistrationViewController


- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)didReceiveMemoryWarning
{
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}


/*
 
 -(IBAction)RegButton{
 //need check// while ([self checkField]!=YES) [self checkField];
 name = nameField.text;
 //  password = passwordField.text;
 //  confirm = confirmField.text;
 
 if ([passwordField.text isEqualToString:confirmField.text]){
 NSLog(@"equal");
 //получить хэш
 password = [passwordField.text dataUsingEncoding:NSUTF8StringEncoding];
 EncryptionData *encryptionData = [[[EncryptionData alloc] init] autorelease];
 NSString *encryptedPass = [encryptionData encryptionPassword:password];
 NSLog(@"%@", encryptedPass);
 
 // отправка на сервер
 ServerCommunication *serverCommunication = [[[ServerCommunication alloc] init: encryptionData] autorelease];
 NSString *serverAnswer = [serverCommunication regUser:name password:encryptedPass email:email.text];
 
 if ([serverAnswer isEqualToString:@"already_exist"]){
 UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Пользователь уже существует" message:@"Попробуйте еще раз" delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
 [alert show];
 [alert release];
 nameField.text=nil;     
 } 
 
 else if ([serverAnswer isEqualToString:@"bad_mail"]){
 UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"E-mail уже используется" message:@"Попробуйте еще раз" delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
 [alert show];
 [alert release];
 email.text=nil;     
 } 
 
 else {
 NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
 [userDefaults setObject:nameField.text forKey:@"login"];
 [userDefaults setObject:encryptedPass forKey:@"password"];
 
 UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Поздравляем!" message:@"Вы зарегистрированы!" delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
 [alert show];
 [alert release];
 
 ProfileViewController *screen = [[ProfileViewController alloc] initWithNibName:@"ProfileView" bundle:nil];
 screen.modalTransitionStyle = UIModalTransitionStyleFlipHorizontal;
 [self presentModalViewController: screen animated:YES];
 [screen release]; 
 //вызвать профайл
 // [self dismissModalViewControllerAnimated:YES];
 }
 }
 else {
 NSLog(@"don't equal");
 UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Пароли не совпадают" message:@"Попробуйте еще раз" delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
 [alert show];
 [alert release];
 
 passwordField.text=nil;
 confirmField.text=nil;
 }
 
 }


 */





#pragma mark - View lifecycle

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view from its nib.
}

- (void)viewDidUnload
{
    nameField = nil;
    passwordField = nil;
    againPasswordField = nil;
    emailField = nil;
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    // Return YES for supported orientations
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

- (IBAction)goButton:(id)sender {

    if ([self checkField]) {
        NSData *password = [passwordField.text dataUsingEncoding:NSUTF8StringEncoding];
        EncryptionData *encryptionData = [[EncryptionData alloc] init];
        NSString *encryptedPass = [encryptionData encryptionPassword:password];
      //  NSLog(@"%@", encryptedPass);
        
        // отправка на сервер
        ServerCommunication *serverCommunication = [[ServerCommunication alloc] init];
        NSString *serverAnswer = [serverCommunication regUser:nameField.text password:encryptedPass email:emailField.text];
        
     //   NSLog(@"answer = %@", serverAnswer);
    }
    
}




- (IBAction)cancelButton:(id)sender {
     [self dismissModalViewControllerAnimated:YES];
}




- (BOOL)checkField{
    if ([nameField.text isEqualToString:@""]) {
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Ошибка!" message:@"поле login не заполнено" delegate:self cancelButtonTitle:@"ОК" otherButtonTitles:nil];
        [alert show];
        return NO;
    } 
    else if ([emailField.text isEqualToString:@""]){
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Ошибка!" message:@"поле email не заполнено" delegate:self cancelButtonTitle:@"ОК" otherButtonTitles:nil];
        [alert show];
        return NO;
    }
    else if ([passwordField.text isEqualToString:@""]){
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Ошибка!" message:@"поле password не заполнено" delegate:self cancelButtonTitle:@"ОК" otherButtonTitles:nil];
        [alert show];
        return NO;
    }
    else if (![passwordField.text isEqualToString:againPasswordField.text]){
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Ошибка!" message:@"пароли не совпадают" delegate:self cancelButtonTitle:@"ОК" otherButtonTitles:nil];
        [alert show];
        return NO;
    }
    else return YES;
}

@end
