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

@interface AuthViewController : UIViewController {
    
    IBOutlet UITextField *passwordField;
    IBOutlet UITextField *loginField;
}

- (IBAction)cancelButton:(id)sender;
- (IBAction)goButton:(id)sender;
- (BOOL)checkData;
- (void)showResult: (NSString *)answer;

@end
