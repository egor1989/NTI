//
//  AuthViewController.h
//  NTI
//
//  Created by Елена on 07.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "EncryptionData.h"
#import "ServerCommunication.h"
#import "HelpViewController.h"

@interface AuthViewController : UIViewController {
    
    IBOutlet UITextField *passwordField;
    IBOutlet UITextField *loginField;
    
}



- (BOOL)checkData;
- (IBAction)authButton:(id)sender;
- (IBAction)regButton:(id)sender;
- (IBAction)forgotButton:(id)sender;
@end
