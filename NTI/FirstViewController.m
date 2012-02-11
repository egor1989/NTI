//
//  FirstViewController.m
//  NTI
//
//  Created by Елена on 05.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "FirstViewController.h"

#define MAX3(a,b,c) ( MAX(a,b)>c ? ((a>b)? 1:2) : 3 )

@implementation FirstViewController
@synthesize fileName;

- (void) accelerometerReciver: (NSNotification*) theNotice{
    userAcceleration=((CMDeviceMotion*)[theNotice.userInfo objectForKey: @"motion"]).userAcceleration;
    gravity=((CMDeviceMotion*)[theNotice.userInfo objectForKey: @"motion"]).gravity;
    maxGravityAxe = MAX3(fabs(gravity.x), fabs(gravity.y), fabs(gravity.z));
    if (maxGravityAxe==1){
        x=userAcceleration.y+gravity.y;
        y=userAcceleration.z+gravity.z;
    }
    else{
        if (maxGravityAxe==2){
            x=userAcceleration.x+gravity.x;
            y=userAcceleration.z+gravity.z;
        }
        else{
            if (maxGravityAxe==3){
                x=userAcceleration.y+gravity.y;
                y=userAcceleration.x+gravity.x;
            }
        }
    }
//    accX.text =[NSString stringWithFormat:@"%d км/ч", [current intValue]];
    accX.text=[NSString stringWithFormat:@"%f", userAcceleration.x];
    accY.text=[NSString stringWithFormat:@"%f", userAcceleration.y];
    accZ.text=[NSString stringWithFormat:@"%f", userAcceleration.z];
    
    time.text = [NSString stringWithFormat:@"%.5f",[[[NSDate alloc ]init]timeIntervalSince1970]];
    
   // if (writeInDB) {
   //     [databaseAction addRecord:currentAcceleration Type:0];
   // }

    if (writeToFile) {

      
        NSDictionary *acc = [NSDictionary dictionaryWithObjectsAndKeys:[NSString stringWithFormat:@"%f", x], @"accX", [NSString stringWithFormat:@"%f", y], @"accY", nil];
        
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
    userDefaults = [NSUserDefaults standardUserDefaults];

   // accelFileNumber = 0;
   // decelFileNumber = 0;
   // leftRotFileNumber = 0;
   // rightRotFileNumber = 0;
   // otherFile = 0;
       
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
     name: @"motionNotification"
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
     name: @"motionNotification"
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
        fileName = [NSString stringWithFormat: @"acceleration%i",[userDefaults integerForKey:@"accelFileNumber"]];
        accelFileNumber = [userDefaults integerForKey:@"accelFileNumber"]+1;
        [userDefaults setInteger:accelFileNumber forKey:@"accelFileNumber"];
        [userDefaults synchronize];
        NSLog(@"accelFileNumber %i", [userDefaults integerForKey:@"accelFileNumber"]);
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
        fileName = [NSString stringWithFormat: @"deceleration%i",[userDefaults integerForKey:@"decelFileNumber"]];
        decelFileNumber = [userDefaults integerForKey:@"decelFileNumber"]+1;
        [userDefaults setInteger:decelFileNumber forKey:@"decelFileNumber"];
        [userDefaults synchronize];

        writeToFile = YES;
        
    }
    else {
        [decelButton setTitle:@"Торможение" forState:UIControlStateNormal];
        writeToFile = NO;
        NSString *JSON = [jsonConvert convert:forJSON];
        [fileController writeToFile:JSON fileName:fileName];

    }
    
    //[databaseAction addRecord:currentAcceleration Type:2];
    
}

- (IBAction)leftRot:(id)sender {
    if (![leftButton.titleLabel.text isEqualToString:@"Stop"]) {
        forJSON = [[NSMutableArray alloc] init];
        [leftButton setTitle:@"Stop" forState:UIControlStateNormal];
        fileName = [NSString stringWithFormat: @"leftRotation%i",[userDefaults integerForKey:@"leftRotFileNumber"]];
        leftRotFileNumber = [userDefaults integerForKey:@"leftRotFileNumber"]+1;
        [userDefaults setInteger:leftRotFileNumber forKey:@"leftRotFileNumber"];
        [userDefaults synchronize];
        writeToFile = YES;
    }
    else {
        [leftButton setTitle:@"<-" forState:UIControlStateNormal];
        writeToFile = NO;
        NSString *JSON = [jsonConvert convert:forJSON];
        [fileController writeToFile:JSON fileName:fileName];

    }

    //[databaseAction addRecord:currentAcceleration Type:3];
}

- (IBAction)rightRot:(id)sender {
    if (![rightButton.titleLabel.text isEqualToString:@"Stop"]) {
        forJSON = [[NSMutableArray alloc] init];
        [rightButton setTitle:@"Stop" forState:UIControlStateNormal];
        fileName = [NSString stringWithFormat: @"rightRotation%i",[userDefaults integerForKey:@"rightRotFileNumber"]];
        rightRotFileNumber = [userDefaults integerForKey:@"rightRotFileNumber"]+1;
        [userDefaults setInteger:rightRotFileNumber forKey:@"rightRotFileNumber"];
        [userDefaults synchronize];

       // rightRotFileNumber++;

        writeToFile = YES;
    }
    else {
        [rightButton setTitle:@"->" forState:UIControlStateNormal];
        writeToFile = NO;
        NSString *JSON = [jsonConvert convert:forJSON];
        [fileController writeToFile:JSON fileName:fileName];

        
    }

    //[databaseAction addRecord:currentAcceleration Type:4];
}


- (IBAction)actionButton:(id)sender {
    NSLog(@"%@", action.titleLabel.text);
    if ([action.titleLabel.text isEqualToString:@"Start"]) {
        forJSON = [[NSMutableArray alloc] init];
        [action setTitle:@"Stop" forState:UIControlStateNormal];
        
        writeToFile = YES;
        fileName = [NSString stringWithFormat:@"other%i", [userDefaults integerForKey:@"otherFile"]];
        otherFile = [userDefaults integerForKey:@"otherFile"]+1;
        [userDefaults setInteger:otherFile forKey:@"otherFile"];
        [userDefaults synchronize];

        
        otherFile++;
       // writeInDB = YES;
        //start write to database
    }
    else {
        [action setTitle:@"Start" forState:UIControlStateNormal];
        writeToFile = NO;
        NSString *JSON = [jsonConvert convert:forJSON];
        [fileController writeToFile:JSON fileName:fileName];
      //  writeInDB =NO;
        //stop write to database
    }
    NSLog(@"push action");
}

- (IBAction)clearDB:(id)sender {
    //[databaseAction clearDatabase];
    [fileController deleteFile];
}

- (IBAction)sendFile:(id)sender {
    //Email *email = [[Email alloc] initWith:self];
    [self sendFile];
}


- (void)sendFile
{
    MFMailComposeViewController *picker = [[MFMailComposeViewController alloc] initWithNibName:@"Email" bundle:nil];
    picker.mailComposeDelegate = self;
    
    
    
	// Set the subject of email
    [picker setSubject:@"NTI!"];
    
	// Add email addresses
    // Notice three sections: "to" "cc" and "bcc"	
	//[picker setToRecipients:[NSArray arrayWithObjects:@"peacock7team@gmail.com", nil]];
    [picker setToRecipients:[NSArray arrayWithObjects:@"alekseenko.lena@gmail.com", nil]];
		
    
	// Fill out the email body text
	NSString *emailBody = @"NTI data";
    
	// This is not an HTML formatted email
	[picker setMessageBody:emailBody isHTML:NO];
    
    

    NSData *attachment = [fileController makeArchive];
    
    // Attach  data to the email
    
	
	[picker addAttachmentData:attachment mimeType:@"application/zip" fileName:@"NTI"];
    

	// Show email view
	
	[self presentModalViewController:picker animated:YES];
    
	// Release picker
    
}

- (void)mailComposeController:(MFMailComposeViewController*)controller didFinishWithResult:(MFMailComposeResult)result error:(NSError*)error 
{	
	[self dismissModalViewControllerAnimated:YES];
    if (error == nil) {
        [fileController deleteFile];
    }
}




@end
