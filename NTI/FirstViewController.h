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
#import "FileController.h"
#import "toJSON.h"




@interface FirstViewController : UIViewController{
    
    IBOutlet UILabel *accX;
    IBOutlet UILabel *accY;
    IBOutlet UILabel *accZ;
    
    IBOutlet UILabel *time;
    IBOutlet UILabel *course;
    IBOutlet UILabel *longitude;
    IBOutlet UILabel *speed;
    
    IBOutlet UILabel *latitude;
    
    CMAcceleration userAcceleration;
    CMAcceleration gravity;
    int maxGravityAxe;
    double x,y;
    
    DatabaseActions *databaseAction;
    FileController *fileController;
    
    IBOutlet UIButton *action;
    NSInteger otherFile;
    NSUserDefaults *userDefaults;
    
    IBOutlet UIButton *accelButton;
    BOOL writeToAccelFile;
    NSInteger accelFileNumber;
    IBOutlet UIButton *decelButton;
    BOOL writeToDecelFile;
    NSInteger decelFileNumber;
    IBOutlet UIButton *rightButton;
    BOOL writeToRightRotFile;
    NSInteger rightRotFileNumber;
    IBOutlet UIButton *leftButton;
    BOOL writeToLeftRotFile;
    NSInteger leftRotFileNumber;
    
    BOOL writeToFile;
    NSString *fileName;
    
    BOOL writeInDB;
    
    NSArray *keys;
    NSMutableArray *forJSON;
   // NSDictionary *entries;
    
    toJSON *jsonConvert;
    
    CLLocation *location;
}

- (IBAction)acceleration:(id)sender;
- (IBAction)deceleration:(id)sender;
- (IBAction)rightRot:(id)sender;
- (IBAction)leftRot:(id)sender;
- (IBAction)actionButton:(id)sender;
- (IBAction)clearDB:(id)sender;


@property (nonatomic, retain) NSString *fileName;


- (void) showGPS;

@end
