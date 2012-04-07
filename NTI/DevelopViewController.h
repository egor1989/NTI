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
#import <MessageUI/MessageUI.h>
#import <MessageUI/MFMailComposeViewController.h>
#import "DatabaseActions.h"
#import "FileController.h"
#import "toJSON.h"
#import "ServerCommunication.h"
#import "CSVConverter.h"

@interface DevelopViewController : UIViewController <UINavigationControllerDelegate, MFMailComposeViewControllerDelegate>{
    
    IBOutlet UILabel *accX;
    IBOutlet UILabel *accY;
    IBOutlet UILabel *accZ;
    
    IBOutlet UILabel *time;
    IBOutlet UILabel *course;
    IBOutlet UILabel *speed;
    IBOutlet UIImageView *rowCourse;
    IBOutlet UILabel *rowDegrees;

    IBOutlet UILabel *northValue;
    IBOutlet UIImageView *northRow;
    IBOutlet UIImageView *gpsRow;
    
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
    IBOutlet UIButton *decelButton;
    IBOutlet UIButton *rightButton;
    IBOutlet UIButton *leftButton;

    IBOutlet UIButton *feedBackButton;
    BOOL writeToFile;
    NSString *fileName;

    
    BOOL writeToDB;
    NSInteger logFile;
    NSString *fileNameCSV;
    
    
    NSArray *keys;
    NSMutableArray *dataArray;
   // NSDictionary *entries;
    
    toJSON *jsonConvert;
    
    CLLocation *location;
    int k;
    
    CSVConverter *csvConverter;
    BOOL writeToLog;
    NSString *type;
    
    IBOutlet UILabel *writeLabel;
    
    IBOutlet UILabel *timerLabel;
}

- (IBAction)acceleration:(id)sender;
- (IBAction)deceleration:(id)sender;
- (IBAction)rightRot:(id)sender;
- (IBAction)leftRot:(id)sender;
- (IBAction)actionButton:(id)sender;
- (IBAction)clearDB:(id)sender;
- (IBAction)sendFile:(id)sender;
- (IBAction)feedBackButton:(id)sender;




@property (nonatomic, retain) NSString *fileName;
@property (nonatomic, retain) NSString *type;


- (void) showGPS;
- (void) sendFile;
- (void) infoAboutFiles;
- (NSString *)convertSize: (NSInteger)size;
- (void)sendToServer;
@end
