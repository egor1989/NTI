//
//  FirstViewController.m
//  NTI
//
//  Created by Елена on 05.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "StatViewController.h"

#define MAX3(a,b,c) ( MAX(a,b)>c ? ((a>b)? 1:2) : 3 )
#define radianConst M_PI/180.0

@implementation StatViewController

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

    if (writeToFile) {
        float curSpeed = 0;
        if (location.speed > 0) curSpeed = location.speed*3.6;
        
        float distance = [myAppDelegate allDistance]/1000;
        
      
        NSDictionary *acc = [NSDictionary dictionaryWithObjectsAndKeys:[NSString stringWithFormat:@"%f", x], @"x", [NSString stringWithFormat:@"%f", y], @"y", nil];
        
        NSDictionary *gps = [NSDictionary dictionaryWithObjectsAndKeys:[NSString stringWithFormat:@"%.1f",[myAppDelegate course]], @"direction", [NSString stringWithFormat:@"%.2f",curSpeed], @"speed", [NSString stringWithFormat:@"%.6f",location.coordinate.latitude], @"latitude", [NSString stringWithFormat:@"%.6f",location.coordinate.longitude], @"longitude", [NSString stringWithFormat:@"%.0f",[myAppDelegate north]], @"compass", [NSString stringWithFormat:@"%.2f",distance], @"distance",    nil];
        
        NSArray *objs = [NSArray arrayWithObjects:  [NSString stringWithFormat:@"%.0f",[[[NSDate alloc ]init]timeIntervalSince1970]*1000], type, acc, gps, nil];
        NSDictionary *entries = [NSDictionary dictionaryWithObjects:objs forKeys:keys];
        
        [forJSON addObject:entries];
        
        NSInteger countInArray = forJSON.count;
       // NSLog(@"countInArray = %i", countInArray);
        //NSLog(@"%@",forJSON);
        
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
    k=0;
    databaseAction = [[DatabaseActions alloc] initDataBase];
    writeInDB = NO;
    userDefaults = [NSUserDefaults standardUserDefaults];

   // accelFileNumber = 0;
   // decelFileNumber = 0;
   // leftRotFileNumber = 0;
   // rightRotFileNumber = 0;
   // otherFile = 0;
       
    keys = [NSArray arrayWithObjects:@"timestamp", @"type", @"acc", @"gps", nil];
    
    jsonConvert = [[toJSON alloc]init];
    fileController = [[FileController alloc] init];
    csvConverter = [[CSVConverter alloc] init];

    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(accelerometerReciver:)
     name: @"motionNotification"
     object: nil];
    
    
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
    


	// Do any additional setup after loading the view, typically from a nib.
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
    
    
/*    if (writeToLog) {
      float curSpeed = location.speed*3.6;
        if (curSpeed<0) {
            curSpeed=0;
        } 
        float distance = [myAppDelegate allDistance]/1000;
        
        
        NSDictionary *acc = [NSDictionary dictionaryWithObjectsAndKeys:[NSString stringWithFormat:@"%f", x], @"x", [NSString stringWithFormat:@"%f", y], @"y", nil];
        
        NSDictionary *gps = [NSDictionary dictionaryWithObjectsAndKeys:[NSString stringWithFormat:@"%.1f",[myAppDelegate course]], @"direction", [NSString stringWithFormat:@"%.2f",curSpeed], @"speed", [NSString stringWithFormat:@"%.6f",location.coordinate.latitude], @"latitude", [NSString stringWithFormat:@"%.6f",location.coordinate.longitude], @"longitude", [NSString stringWithFormat:@"%.0f",[myAppDelegate north]], @"compass", [NSString stringWithFormat:@"%.2f",distance], @"distance",   nil];
        
        NSArray *objs = [NSArray arrayWithObjects:  [NSString stringWithFormat:@"%.0f",[[[NSDate alloc ]init]timeIntervalSince1970]*1000], acc,gps, nil];
        NSDictionary *entries = [NSDictionary dictionaryWithObjects:objs forKeys:keys];
        
        [forJSON addObject:entries];
        
        NSInteger countInArray = forJSON.count;
        NSLog(@"countInArray = %i", countInArray);
        NSLog(@"%@",forJSON);
        
    }
 */

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
        forJSON = [[NSMutableArray alloc] init];
        [action setTitle:@"Stop" forState:UIControlStateNormal];
        type = @"-";
        writeToFile = YES;
        fileNameCSV = [NSString stringWithFormat:@"log%i.csv", [userDefaults integerForKey:@"otherFile"]];
        fileName = [NSString stringWithFormat:@"log%i", [userDefaults integerForKey:@"otherFile"]];
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
        NSString *CSV = [csvConverter arrayToCSVString:forJSON];
        [fileController writeToFile:CSV fileName:fileNameCSV];
        [fileController writeToFile:JSON fileName:fileName];
      //  writeInDB =NO;
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
        //[databaseAction clearDatabase];
       [fileController deleteFile]; 
    }
    
	//NSLog(@"Index - %i, title - %@", buttonIndex, [alertView buttonTitleAtIndex:buttonIndex]);
}




- (IBAction)sendFile:(id)sender {
    
    //[self sendFile];
    [self infoAboutFiles];
    
    
  //  NSLog(@"tampampam");
  //  UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Запрос на удаление" message:@"Вы хотите удалить отправленные файлы?" delegate:self cancelButtonTitle:@"Отмена" otherButtonTitles:@"Да",nil];
  //  [alert show];
    
}




- (void)sendFile
{
    MFMailComposeViewController *picker = [[MFMailComposeViewController alloc] initWithNibName:@"Email" bundle:nil];
    picker.mailComposeDelegate = self;
    
    
    
	// Set the subject of email
    [picker setSubject:@"NTI"];
    
	// Add email addresses
    // Notice three sections: "to" "cc" and "bcc"	
    [picker setToRecipients:[NSArray arrayWithObjects:@"alekseenko.lena@gmail.com", /* @"peacock7team@gmail.com", */nil]];
    //[picker setBccRecipients:<#(NSArray *)#>
		
    
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
    ServerCommunication *serverCommunication = [[ServerCommunication alloc] init];
    NSMutableArray *allFiles = [fileController getAllFiles];
    for (NSString *file in allFiles) {
        NSLog(@"%@",file);
        NSString *content = [fileController readFile:file];
        [serverCommunication uploadData:content];
    }
}

- (void)mailComposeController:(MFMailComposeViewController*)controller didFinishWithResult:(MFMailComposeResult)result error:(NSError*)error 
{	
	[self dismissModalViewControllerAnimated:YES];
    if (error == nil) {
        [fileController deleteFile];
    }
}


@end
