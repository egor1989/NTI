//
//  FirstViewController.m
//  NTI
//
//  Created by Елена on 05.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "FirstViewController.h"

@implementation FirstViewController

- (void) accelerometerReciver: (NSNotification*) theNotice{
    currentAcceleration=((CMAccelerometerData*)[theNotice.userInfo objectForKey: @"accel"]).acceleration;
//    accX.text =[NSString stringWithFormat:@"%d км/ч", [current intValue]];
    accX.text=[NSString stringWithFormat:@"%f", currentAcceleration.x];
    accY.text=[NSString stringWithFormat:@"%f", currentAcceleration.y];
    accZ.text=[NSString stringWithFormat:@"%f", currentAcceleration.z];
    
    time.text = [NSString stringWithFormat:@"%.5f",[[[NSDate alloc ]init]timeIntervalSince1970]];
    if (writeInDB) {
        [databaseAction addRecord:currentAcceleration Type:0];
    }
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Release any cached data, images, etc that aren't in use.
}

#pragma mark - View lifecycle

- (void)viewDidLoad
{
    
    
    [super viewDidLoad];
    databaseAction = [[DatabaseActions alloc] initDataBase];
    writeInDB = NO;
	// Do any additional setup after loading the view, typically from a nib.
}

- (void)viewDidUnload
{
    accX = nil;
    accY = nil;
    accZ = nil;
    latitude = nil;
    speed = nil;
    longitude = nil;
    course = nil;
    time = nil;

    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(showGPS)
     name: @"locateNotification"
     object: nil]; 

    
    
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(accelerometerReciver:)
     name: @"accelNotification"
     object: nil];
}

- (void)viewWillDisappear:(BOOL)animated
{
	[super viewWillDisappear:animated];
}

- (void)viewDidDisappear:(BOOL)animated
{
    [super viewDidDisappear:animated];
    
    [[NSNotificationCenter defaultCenter]	
     removeObserver: self
     name: @"locateNotification"
     object: nil]; 

    
    [[NSNotificationCenter defaultCenter]	
     removeObserver: self
     name: @"accelNotification"
     object: nil];
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    // Return YES for supported orientations
    return (interfaceOrientation != UIInterfaceOrientationPortraitUpsideDown);
}


- (void) showGPS{
    CLLocation *location = [myAppDelegate lastLoc];  
    //NSLog(@"lat = %@, lond = %@", [NSString stringWithFormat:@"%f", location.coordinate.latitude], [NSString stringWithFormat:@"%f", location.coordinate.longitude]);
    course.text = [NSString stringWithFormat:@"%.2f",location.course];
    longitude.text = [NSString stringWithFormat:@"%.6f", location.coordinate.longitude]; 
    speed.text =  [NSString stringWithFormat:@"%.2f", location.speed];
    latitude.text = [NSString stringWithFormat:@"%.6f", location.coordinate.latitude];

}


- (IBAction)acceleration:(id)sender {
    NSLog(@"push acceleration");
    [databaseAction addRecord:currentAcceleration Type:1];
}

- (IBAction)deceleration:(id)sender {
     NSLog(@"push deceleration");
    [databaseAction addRecord:currentAcceleration Type:2];
    
}

- (IBAction)rotation:(id)sender {
    [databaseAction addRecord:currentAcceleration Type:3];
}

- (IBAction)actionButton:(id)sender {
    NSLog(@"%@", action.titleLabel.text);
    if ([action.titleLabel.text isEqualToString:@"Start"]) {
        [action setTitle:@"Stop" forState:UIControlStateNormal];
        writeInDB = YES;
        //start write to database
    }
    else {
        [action setTitle:@"Start" forState:UIControlStateNormal];
        writeInDB =NO;
        //stop write to database
    }
    NSLog(@"push action");
}

- (IBAction)clearDB:(id)sender {
    [databaseAction clearDatabase];
}
@end
