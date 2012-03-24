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
        [nameField becomeFirstResponder];
        
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
        [serverCommunication regUser:nameField.text password:encryptedPass email:emailField.text];
        //[serverCommunication checkErrors: serverAnswer];

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
