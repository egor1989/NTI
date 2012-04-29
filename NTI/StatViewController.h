//
//  StatViewController.h
//  NTI
//
//  Created by Mike on 07.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "AppDelegate.h"
#import "AuthViewController.h"
#import "RecordAction.h"
#import "ServerCommunication.h"
#import "StatHelpViewController.h"


#define myAppDelegate (AppDelegate*) [[UIApplication sharedApplication] delegate]

@interface StatViewController : UITableViewController{
    UILabel *speedLabel;
    IBOutlet UIButton *loginButton;
    UILabel *qualityDriving;
    UILabel *speedMode;
    UILabel *acceleration;
    UILabel *deceleration;
    UILabel *rotation;
    UILabel *recordLabel;
    IBOutlet UIButton *sendButton;
    ServerCommunication *serverCommunication;
    UIImageView *recordImage;
    IBOutlet UIButton *helpButton;
    
}

@property (nonatomic) BOOL *writeAction;

- (void) speedUpdate;
- (IBAction)loginButton:(id)sender;
- (IBAction)sendButton:(id)sender;
- (IBAction)helpButton:(id)sender;
- (void) pickOne:(id)sender;
- (void)changeImage;
- (void)parse:(NSString *)result;
- (IBAction) internetUploadSwitch:(id)sender;
@end
