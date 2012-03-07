//
//  RegistrationViewController.h
//  NTI
//
//  Created by Елена on 07.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface RegistrationViewController : UIViewController{
    
    IBOutlet UITextField *emailField;
    IBOutlet UITextField *againPasswordField;
    IBOutlet UITextField *passwordField;
    IBOutlet UITextField *nameField;

}
- (IBAction)goButton:(id)sender;
- (IBAction)cancelButton:(id)sender;
- (BOOL)checkField;


@end
