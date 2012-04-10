//
//  FirstViewController.m
//  NTI
//
//  Created by Елена on 05.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "DevelopViewController.h"

#define MAX3(a,b,c) ( MAX(a,b)>c ? ((a>b)? 1:2) : 3 )
#define radianConst M_PI/180.0
#define maxEntries 10

@implementation DevelopViewController

@synthesize fileName, type;

- (void) accelerometerReciver: (NSNotification*) theNotice{
    userAcceleration=((CMDeviceMotion*)[theNotice.userInfo objectForKey: @"motion"]).userAcceleration;
    gravity=((CMDeviceMotion*)[theNotice.userInfo objectForKey: @"motion"]).gravity;
    maxGravityAxe = MAX3(fabs(gravity.x), fabs(gravity.y), fabs(gravity.z));
    if (maxGravityAxe==1){
        x=-userAcceleration.z;
        y=userAcceleration.y;
    }
    else{
        if (maxGravityAxe==2){
            x=-userAcceleration.x;
            y=-userAcceleration.z;
        }
        else{
            if (maxGravityAxe==3){
                x=userAcceleration.x;//!!
                y=userAcceleration.y;
            }
        }
    }
    if (k%3==0) {
        NSDictionary *dict = [NSDictionary dictionaryWithObjectsAndKeys:[NSNumber numberWithDouble:x], @"accX", [NSNumber numberWithDouble:y], @"accY", nil];
        [[NSNotificationCenter defaultCenter]	postNotificationName:	@"plotNotification" 
                                                            object:  nil
                                                          userInfo:dict];
    }
    k++;
//    accX.text =[NSString stringWithFormat:@"%d км/ч", [current intValue]];
    accX.text=[NSString stringWithFormat:@"%f", userAcceleration.x];
    accY.text=[NSString stringWithFormat:@"%f", userAcceleration.y];
    accZ.text=[NSString stringWithFormat:@"%f", userAcceleration.z];
    
    time.text = [NSString stringWithFormat:@"%.5f",[[[NSDate alloc ]init]timeIntervalSince1970]]; 
    
   // if (writeInDB) {
   //     [databaseAction addRecord:currentAcceleration Type:0];
   // }

    if (writeToDB) {
        float curSpeed = 0;
        if (location.speed > 0) curSpeed = location.speed*3.6;
        
        float distance = [myAppDelegate allDistance]/1000;
        
        NSArray *objs = [NSArray arrayWithObjects:  [NSString stringWithFormat:@"%.0f",[[[NSDate alloc ]init]timeIntervalSince1970]*1000], type, [NSString stringWithFormat:@"%f", x], [NSString stringWithFormat:@"%f", y], [NSString stringWithFormat:@"%.0f",[myAppDelegate north]], [NSString stringWithFormat:@"%.1f",[myAppDelegate course]], [NSString stringWithFormat:@"%.2f",distance], [NSString stringWithFormat:@"%.6f",location.coordinate.latitude],[NSString stringWithFormat:@"%.6f",location.coordinate.longitude], [NSString stringWithFormat:@"%.2f",curSpeed], nil];
        NSDictionary *entries = [NSDictionary dictionaryWithObjects: objs forKeys:keys];
        [dataArray addObject:entries];
        
        NSInteger countInArray = dataArray.count;
       
        if (countInArray > maxEntries){ 
            //countInArray = 0;
            NSMutableArray *toWrite = dataArray;
            dataArray = [[NSMutableArray alloc] init];
            //создаем новый тред
            NSThread* myThread = [[NSThread alloc] initWithTarget:databaseAction
                                                         selector:@selector(addArray:)
                                                           object:toWrite];
            [myThread start]; 
        }
        
        NSLog(@"countInArray = %i", countInArray);
    }
    if ([myAppDelegate canWriteToFile]) writeLabel.text = @"+";
    else writeLabel.text = @"-";

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
    k=0;
    writeToDB = NO;
    userDefaults = [NSUserDefaults standardUserDefaults];
       
    keys = [NSArray arrayWithObjects:@"timestamp", @"type", @"accX", @"accY", @"compass", @"direction", @"distance", @"latitude", @"longitude",@"speed", nil];
    
    jsonConvert = [[toJSON alloc]init];
    fileController = [[FileController alloc] init];
    csvConverter = [[CSVConverter alloc] init];

    //[[NSNotificationCenter defaultCenter]	
   // addObserver: self
   //  selector: @selector(accelerometerReciver:)
   //  name: @"motionNotification"
   //  object: nil];
    
    
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(showGPS)
     name: @"locateNotification"
     object: nil]; 
    
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(redrawCourse)
     name: @"redrawCourse"
     object: nil]; 
    
}



- (void)viewDidUnload
{
    accX = nil;
    accY = nil;
    accZ = nil;

    speed = nil;

    course = nil;
    time = nil;

    gpsRow = nil;
    northRow = nil;
    northValue = nil;
    
    writeLabel = nil;
    timerLabel = nil;
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
}

- (void)viewWillDisappear:(BOOL)animated
{
	[super viewWillDisappear:animated];
}

- (void)viewDidDisappear:(BOOL)animated
{
    [super viewDidDisappear:animated];
    
//    [[NSNotificationCenter defaultCenter]	
//     removeObserver: self
//     name: @"locateNotification"
//     object: nil]; 

    
//    [[NSNotificationCenter defaultCenter]	
//     removeObserver: self
//     name: @"motionNotification"
//     object: nil];
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
    if (location.course <=0) {
        gpsRow.hidden = YES;
    }
    else {
        gpsRow.hidden = NO;
        gpsRow.transform = CGAffineTransformMakeRotation(location.course*radianConst);
    }
  
    if (location.speed <= 0) speed.text = @"0";
    else speed.text =  [NSString stringWithFormat:@"%.2f", location.speed*3.6];
    
    
    //


}

- (void) redrawCourse{
    if (gpsRow.hidden == YES) rowCourse.hidden = YES;
    else {
        rowCourse.hidden = NO;
        rowCourse.transform = CGAffineTransformMakeRotation([myAppDelegate course]*radianConst);
    }
    rowDegrees.text = [NSString stringWithFormat:@"%.2f", [myAppDelegate course]];
    
    northValue.text = [NSString stringWithFormat:@"%.2f", [myAppDelegate trueNorth]];
    northRow.transform = CGAffineTransformMakeRotation([myAppDelegate trueNorth]*radianConst); 
    
}


- (IBAction)feedBackButton:(id)sender{
    
}

- (IBAction)acceleration:(id)sender {
    NSLog(@"push acceleration");
    if (![accelButton.titleLabel.text isEqualToString:@"Stop"]) {
        [accelButton setTitle:@"Stop" forState:UIControlStateNormal];
        type = @"acceleration";
    }
    else {
       
        [accelButton setTitle:@"Ускорение" forState:UIControlStateNormal];
        type = @"-";
    }
        //[databaseAction addRecord:currentAcceleration Type:1];
}

- (IBAction)deceleration:(id)sender {
     NSLog(@"push deceleration");
    if (![decelButton.titleLabel.text isEqualToString:@"Stop"]) {
        [decelButton setTitle:@"Stop" forState:UIControlStateNormal];
        type = @"deceleration";
        //forJSON = [[NSMutableArray alloc] init];
        //fileName = [NSString stringWithFormat: @"deceleration%i",[userDefaults integerForKey:@"decelFileNumber"]];
        //decelFileNumber = [userDefaults integerForKey:@"decelFileNumber"]+1;
        //[userDefaults setInteger:decelFileNumber forKey:@"decelFileNumber"];
        //[userDefaults synchronize];

        //writeToFile = YES;
        
    }
    else {
        [decelButton setTitle:@"Торможение" forState:UIControlStateNormal];
        type = @"-";
        //writeToFile = NO;
        //NSString *JSON = [jsonConvert convert:forJSON];
        //[fileController writeToFile:JSON fileName:fileName];

    }
    
    //[databaseAction addRecord:currentAcceleration Type:2];
    
}

- (IBAction)leftRot:(id)sender {
    if (![leftButton.titleLabel.text isEqualToString:@"Stop"]) {
        [leftButton setTitle:@"Stop" forState:UIControlStateNormal];
        type = @"left";
    }
    else {
        [leftButton setTitle:@"<-" forState:UIControlStateNormal];
        type = @"-";

    }

    //[databaseAction addRecord:currentAcceleration Type:3];
}

- (IBAction)rightRot:(id)sender {
    if (![rightButton.titleLabel.text isEqualToString:@"Stop"]) {
            [rightButton setTitle:@"Stop" forState:UIControlStateNormal];
            type=@"right";
    }
    else {
        [rightButton setTitle:@"->" forState:UIControlStateNormal];
        type = @"-";
    }
}





- (IBAction)actionButton:(id)sender {
    NSLog(@"%@", action.titleLabel.text);
    if ([action.titleLabel.text isEqualToString:@"Start"]) {
        dataArray = [[NSMutableArray alloc] init];
        [action setTitle:@"Stop" forState:UIControlStateNormal];
        type = @"-";

        writeToDB = YES;
        //start write to database
    }
    else {
        [action setTitle:@"Start" forState:UIControlStateNormal];
        writeToDB =NO;
        NSMutableArray *toWrite = dataArray;
        dataArray = [[NSMutableArray alloc] init];
        //создаем новый тред
        NSThread* myThread = [[NSThread alloc] initWithTarget:databaseAction
                                                     selector:@selector(addArray:)
                                                       object:toWrite];
        [myThread start]; 
        
        //stop write to database
    }
    NSLog(@"push action");
}

- (IBAction)clearDB:(id)sender {
    
    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Подтвержение" message:@"Вы действительно хотите удалить все файлы?" delegate:self cancelButtonTitle:@"Отмена" otherButtonTitles:@"Да",nil];
    [alert show];
    

    }


- (void) alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex {
    
    if ([alertView.title isEqualToString:@"Отправление файлов"]) {
        if (buttonIndex == 1) {
             NSLog(@"продолжить");
            [self sendFile];
        }
        //NSLog(@"neb");
        //[self sendToServer];
        //[fileController sendFile];
    }
    else if (buttonIndex ==1) {
        [databaseAction clearDatabase];
       //[fileController deleteFile]; 
    }
    
	//NSLog(@"Index - %i, title - %@", buttonIndex, [alertView buttonTitleAtIndex:buttonIndex]);
}




- (IBAction)sendFile:(id)sender {
    [databaseAction readDatabase]; 
}




- (void)sendFile
{
    MFMailComposeViewController *picker = [[MFMailComposeViewController alloc] initWithNibName:@"Email" bundle:nil];
    picker.mailComposeDelegate = self;
    
    
    
	// Set the subject of email
    [picker setSubject:@"NTI"];
    
	// Add email addresses
    // Notice three sections: "to" "cc" and "bcc"	
    [picker setToRecipients:[NSArray arrayWithObjects:@"alekseenko.lena@gmail.com",  @"peacock7team@gmail.com", nil]];		
    
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

- (NSString *)convertSize: (NSInteger)size{
    NSString *result = nil;
    NSInteger kb = size/1024;
    NSLog(@"%i",kb);
    if (kb>1024) {
        NSInteger mb = (int)kb/1024;
        result = [NSString stringWithFormat:@"%i mb %i kb",mb,kb];
    }
    else result = [NSString stringWithFormat:@"%i kb",kb];
    return result;
}



- (void)infoAboutFiles
{
    [fileController countFiles];
    
    NSLog(@"count = %i, size = %@", fileController.fileCount,  [self convertSize: fileController.size]);
    NSString *message = [NSString stringWithFormat:@"Отправить: %i файла(ов) общим размером %@. Продолжить?", fileController.fileCount,  [self convertSize: fileController.size]];
    
    UIAlertView *sendAlert = [[UIAlertView alloc] initWithTitle:@"Отправление файлов" message: message delegate:self cancelButtonTitle:@"Отмена" otherButtonTitles:@"Продолжить",nil];
    [sendAlert show];
    
    
    
}

-(void) sendToServer{
    [databaseAction readDatabase];

}

- (void)mailComposeController:(MFMailComposeViewController*)controller didFinishWithResult:(MFMailComposeResult)result error:(NSError*)error 
{	
	[self dismissModalViewControllerAnimated:YES];
    if (error == nil) {
        [fileController deleteFile];
    }
}


@end
