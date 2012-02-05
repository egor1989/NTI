//
//  FirstViewController.h
//  NTI
//
//  Created by Елена on 05.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>

#import "AppDelegate.h"
#define myAppDelegate (AppDelegate*) [[UIApplication sharedApplication] delegate]

#import <CoreMotion/CoreMotion.h>
#import "DatabaseActions.h"


@interface FirstViewController : UIViewController{
    
    IBOutlet UILabel *accX;
    IBOutlet UILabel *accY;
    IBOutlet UILabel *accZ;
    
    IBOutlet UILabel *time;
    IBOutlet UILabel *course;
    IBOutlet UILabel *longitude;
    IBOutlet UILabel *speed;
    IBOutlet UIButton *action;
    IBOutlet UILabel *latitude;
    
    CMAcceleration currentAcceleration;
    
    DatabaseActions *databaseAction;
    BOOL writeInDB;
}

- (IBAction)acceleration:(id)sender;
- (IBAction)deceleration:(id)sender;
- (IBAction)rotation:(id)sender;
- (IBAction)actionButton:(id)sender;
- (IBAction)clearDB:(id)sender;

- (void) showGPS;

@end
