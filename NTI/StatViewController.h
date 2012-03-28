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

#define myAppDelegate (AppDelegate*) [[UIApplication sharedApplication] delegate]

@interface StatViewController : UITableViewController{
    UILabel *speedLabel;
    IBOutlet UIButton *loginButton;
    UILabel *qualityDriving;
    UILabel *speedMode;
    UILabel *acceleration;
    UILabel *deceleration;
    UILabel *rotation;
}

- (void) speedUpdate;
- (IBAction)loginButton:(id)sender;
- (void) pickOne:(id)sender;

@end
