//
//  FirstViewController.m
//  NTI
//
//  Created by Елена on 05.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "FirstViewController.h"

@implementation FirstViewController
@synthesize fileName;

- (void) accelerometerReciver: (NSNotification*) theNotice{
    currentAcceleration=((CMAccelerometerData*)[theNotice.userInfo objectForKey: @"accel"]).acceleration;
//    accX.text =[NSString stringWithFormat:@"%d км/ч", [current intValue]];
    accX.text=[NSString stringWithFormat:@"%f", currentAcceleration.x];
    accY.text=[NSString stringWithFormat:@"%f", currentAcceleration.y];
    accZ.text=[NSString stringWithFormat:@"%f", currentAcceleration.z];
    
    time.text = [NSString stringWithFormat:@"%.5f",[[[NSDate alloc ]init]timeIntervalSince1970]];
    
   // if (writeInDB) {
   //     [databaseAction addRecord:currentAcceleration Type:0];
   // }

    if (writeToFile) {
       // double timestamp = [[[NSDate alloc ]init]timeIntervalSince1970];
        
      
        
        //вызвать вычисление x y здесь и записать вместо предыдущих 
      
        NSDictionary *acc = [NSDictionary dictionaryWithObjectsAndKeys:[NSString stringWithFormat:@"%f", currentAcceleration.x], @"accX", [NSString stringWithFormat:@"%f", currentAcceleration.y], @"accY", nil];
        
        NSDictionary *gps = [NSDictionary dictionaryWithObjectsAndKeys:[NSString stringWithFormat:@"%.2f",location.course], @"direction", [NSString stringWithFormat:@"%.2f",location.speed*3,6], @"speed", nil];
        
        NSArray *objs = [NSArray arrayWithObjects:  [NSString stringWithFormat:@"%.5f",[[[NSDate alloc ]init]timeIntervalSince1970]], acc,gps, nil];
        NSDictionary *entries = [NSDictionary dictionaryWithObjects:objs forKeys:keys];
        
        [forJSON addObject:entries];
        
        NSInteger countInArray = forJSON.count;
        NSLog(@"countInArray = %i", countInArray);
   
        NSLog(@"write");
        
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
    
    accelFileNumber = 0;
    decelFileNumber = 0;
    leftRotFileNumber = 0;
    rightRotFileNumber = 0;
    otherFile = 0;
   // forJSON = [[NSMutableArray alloc] init];
    
    keys = [NSArray arrayWithObjects:@"timestamp", @"acc", @"gps", nil];
    
    jsonConvert = [[toJSON alloc]init];
    fileController = [[FileController alloc] init];

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
    location = [myAppDelegate lastLoc];  
    //NSLog(@"lat = %@, lond = %@", [NSString stringWithFormat:@"%f", location.coordinate.latitude], [NSString stringWithFormat:@"%f", location.coordinate.longitude]);
    course.text = [NSString stringWithFormat:@"%.2f",location.course];
    longitude.text = [NSString stringWithFormat:@"%.6f", location.coordinate.longitude];
    if (location.speed <= 0) speed.text = @"0";
    else speed.text =  [NSString stringWithFormat:@"%.2f", location.speed*3,6];
    latitude.text = [NSString stringWithFormat:@"%.6f", location.coordinate.latitude];

}


- (IBAction)acceleration:(id)sender {
    NSLog(@"push acceleration");
    if (![accelButton.titleLabel.text isEqualToString:@"Stop"]) {
        forJSON = [[NSMutableArray alloc] init];
       // [accelButton setBackgroundColor:[UIColor greenColor]];
        [accelButton setTitle:@"Stop" forState:UIControlStateNormal];
        writeToFile = YES;
        fileName = [NSString stringWithFormat: @"acceleration%i",accelFileNumber];
        accelFileNumber++;
        NSLog(@"%@", fileName);
    }
    else {
       // [accelButton setBackgroundColor:[UIColor whiteColor]];
        [accelButton setTitle:@"Ускорение" forState:UIControlStateNormal];
        writeToFile = NO;
        
        NSString *JSON = [jsonConvert convert:forJSON];
        [fileController writeToFile:JSON fileName:fileName];
        //[forJSON removeAllObjects];
        
    }
        //[databaseAction addRecord:currentAcceleration Type:1];
}

- (IBAction)deceleration:(id)sender {
     NSLog(@"push deceleration");
    if (![decelButton.titleLabel.text isEqualToString:@"Stop"]) {
        forJSON = [[NSMutableArray alloc] init];
        decelFileNumber++;
        [decelButton setTitle:@"Stop" forState:UIControlStateNormal];
        fileName = [NSString stringWithFormat: @"deceleration%i",decelFileNumber];
        writeToFile = YES;
        
    }
    else {
        [decelButton setTitle:@"Торможение" forState:UIControlStateNormal];
        writeToFile = NO;
        [jsonConvert convert:forJSON];
    }
    
    //[databaseAction addRecord:currentAcceleration Type:2];
    
}

- (IBAction)leftRot:(id)sender {
    if (![leftButton.titleLabel.text isEqualToString:@"Stop"]) {
        forJSON = [[NSMutableArray alloc] init];
        [leftButton setTitle:@"Stop" forState:UIControlStateNormal];
        fileName = [NSString stringWithFormat: @"leftRotation%i",leftRotFileNumber];
        leftRotFileNumber++;
        writeToFile = YES;
    }
    else {
        [leftButton setTitle:@"<-" forState:UIControlStateNormal];
        writeToFile = NO;
        [jsonConvert convert:forJSON];
    }

    //[databaseAction addRecord:currentAcceleration Type:3];
}

- (IBAction)rightRot:(id)sender {
    if (![rightButton.titleLabel.text isEqualToString:@"Stop"]) {
        forJSON = [[NSMutableArray alloc] init];
        [rightButton setTitle:@"Stop" forState:UIControlStateNormal];
        fileName = [NSString stringWithFormat: @"rightRotation%i",rightRotFileNumber];
        rightRotFileNumber++;

        writeToFile = YES;
    }
    else {
        [rightButton setTitle:@"->" forState:UIControlStateNormal];
        writeToFile = NO;
        [jsonConvert convert:forJSON];
        
    }

    //[databaseAction addRecord:currentAcceleration Type:4];
}


- (IBAction)actionButton:(id)sender {
    NSLog(@"%@", action.titleLabel.text);
    if ([action.titleLabel.text isEqualToString:@"Start"]) {
        forJSON = [[NSMutableArray alloc] init];
        [action setTitle:@"Stop" forState:UIControlStateNormal];
        
        writeToFile = YES;
        fileName = [NSString stringWithFormat:@"other%i", otherFile];
        otherFile++;
       // writeInDB = YES;
        //start write to database
    }
    else {
        [action setTitle:@"Start" forState:UIControlStateNormal];
        writeToFile = NO;
        [jsonConvert convert:forJSON];
        
      //  writeInDB =NO;
        //stop write to database
    }
    NSLog(@"push action");
}

- (IBAction)clearDB:(id)sender {
    [databaseAction clearDatabase];
}



@end
