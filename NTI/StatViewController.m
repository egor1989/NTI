//
//  StatViewController.m
//  NTI
//
//  Created by Mike on 07.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "StatViewController.h"

#define ROWSNUMBER 12
#define firstTitle @"Статистика"
#define secondTitle @"Информация"

@implementation StatViewController
@synthesize writeAction, tables, statTableView;
- (id)initWithStyle:(UITableViewStyle)style
{
    self = [super initWithStyle:style];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)didReceiveMemoryWarning
{
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

#pragma mark - View lifecycle
            
- (void)viewDidLoad
{
    NSLog(@"StatView did load");
    [super viewDidLoad];
    UIFont *fontForLabel = [UIFont fontWithName:@"Trebuchet MS" size:16]; 
    
    
    
    
    /************инициализация лейблов для таблицы**********************/
    speedLabel = [[UILabel alloc] initWithFrame:CGRectMake(0.0f, 0.0f, 79.0f, 27.0f)];;
    speedLabel.font =            fontForLabel;
    speedLabel.textAlignment =   UITextAlignmentRight;
    speedLabel.text =            [NSString stringWithFormat:@"%d км/ч", 0];
    
    qualityDriving = [[UILabel alloc] initWithFrame:CGRectMake(0.0f, 0.0f, 79.0f, 27.0f)];;
    qualityDriving.font = fontForLabel;
    qualityDriving.textAlignment = UITextAlignmentRight;
    
    
    speedMode = [[UILabel alloc] initWithFrame:CGRectMake(0.0f, 0.0f, 79.0f, 27.0f)];;
    speedMode.font = fontForLabel;
    speedMode.textAlignment = UITextAlignmentRight;
    

    acceleration = [[UILabel alloc] initWithFrame:CGRectMake(0.0f, 0.0f, 79.0f, 27.0f)];;
    acceleration.font = fontForLabel;
    acceleration.textAlignment = UITextAlignmentRight;
    
    
    deceleration = [[UILabel alloc] initWithFrame:CGRectMake(0.0f, 0.0f, 79.0f, 27.0f)];;
    deceleration.font = fontForLabel;
    deceleration.textAlignment = UITextAlignmentRight;
    
    
    rotation = [[UILabel alloc] initWithFrame:CGRectMake(0.0f, 0.0f, 79.0f, 27.0f)];;
    rotation.font = fontForLabel;
    rotation.textAlignment = UITextAlignmentRight;
    
    countKm = [[UILabel alloc] initWithFrame:CGRectMake(0.0f, 0.0f, 79.0f, 27.0f)];;
    countKm.font = fontForLabel;
    countKm.textAlignment = UITextAlignmentRight;
    
    
    lastTrip = [[UILabel alloc] initWithFrame:CGRectMake(0.0f, 0.0f, 120.0f, 27.0f)];;
    lastTrip.font = fontForLabel;
    lastTrip.textAlignment = UITextAlignmentRight;
    
    recordImage = [[UIImageView alloc] initWithFrame:CGRectMake(280.0f, 7.0f, 27.0f, 27.0f)];
    
    /***************************************************************/
    
    if ([myAppDelegate canWriteToFile]) {
        NSLog(@"green logo");
        [recordImage setImage:[UIImage imageNamed:@"green.png"]];

    }
    else {
        NSLog(@"red logo");
        [recordImage setImage:[UIImage imageNamed:@"red.png"]];
    }
    
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(changeImage)
     name: @"canWriteToFile"
     object: nil];
    [serverCommunication refreshCookie];
    /************ инициализация элементов *******************/

    NSArray *info = [NSArray arrayWithObjects:@"Имя", @"Запись", @"Скорость", @"Только Wi-Fi", @"Дата посл. поезки",@"Тестовый файл", nil];
    NSArray *statistics = [NSArray arrayWithObjects:@"",@"Общая оценка", @"Километраж", @"Превышение скорости", @"Качество разгонов", @"Качество торможений", @"Качество поворотов", nil];
    self.tables = [NSDictionary dictionaryWithObjectsAndKeys:statistics, firstTitle  , info, secondTitle, nil];
}


- (void)changeImage{
    
    if ([myAppDelegate canWriteToFile]) {
        NSLog(@"Green logo");
        [recordImage setImage:[UIImage imageNamed:@"green.png"]];
    }
    else {
         NSLog(@"Red logo");
        [recordImage setImage:[UIImage imageNamed:@"red.png"]];
    }
    
}

- (void)viewDidUnload
{
     NSLog(@"StatView unload");
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (void)viewDidAppear:(BOOL)animated
{
     [self.statTableView reloadData];
     NSLog(@"StatView didAppear");
    
   
    NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
    if ([userDefaults integerForKey:@"segment"]==0) [self parse:[userDefaults valueForKey:@"lastStat"] method:@"lastStat"];
    else [self parse:[userDefaults valueForKey:@"allStat"] method:@"allStat"];
    
    
    //Прослушка notifications
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(speedUpdate)
     name: @"locateNotification"
     object: nil]; 
    [super viewDidAppear:animated];

}

- (void)viewDidDisappear:(BOOL)animated
{
     NSLog(@"StatView didDisappear");
    [super viewDidDisappear:animated];
    
    [[NSNotificationCenter defaultCenter]	
     removeObserver: self
     name: @"locateNotification"
     object: nil]; 
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    // Return YES for supported orientations
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

- (void) speedUpdate{
    double speed =  [myAppDelegate lastLoc].speed*3.6;
    if (speed < 0) speed = 0;
    else speedLabel.text =[NSString stringWithFormat:@"%.0f км/ч", speed];
}

- (NSArray *)curentEntries:(NSInteger)index {
    NSArray *keys = [tables allKeys];
    NSString *curentKey = [keys objectAtIndex:index];
    NSArray *curentEntrie = [tables objectForKey:curentKey];
    return curentEntrie;
}


#pragma mark - Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
//#warning Potentially incomplete method implementation.
    // Return the number of sections.
    return [tables count];
}
 

- (NSString *)tableView:(UITableView *)tableView titleForHeaderInSection:(NSInteger)section

{
    return [[tables allKeys] objectAtIndex:section];
}
 

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    NSArray *curentEntrie = [self curentEntries:section];
    return  [curentEntrie count];
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    
     NSArray *curentEntrie = [self curentEntries:indexPath.section];
    // cell.textLabel.text = [curentEntrie objectAtIndex:indexPath.row];
    
    if (indexPath.section == 0){

        switch( [indexPath row] ){
            case 0: {
                static NSString *CellIdentifier = @"Name";
                UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
                if (cell == nil) {
                cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue1 reuseIdentifier:CellIdentifier];
                    cell.backgroundColor = [UIColor whiteColor];
                cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                cell.textLabel.text = [curentEntrie objectAtIndex:indexPath.row];
                //NSLog(@"login = %@", [[NSUserDefaults standardUserDefaults]  objectForKey:@"login"]);
                cell.detailTextLabel.text = [[NSUserDefaults standardUserDefaults] objectForKey:@"login"];
 
                    
                loginButton = [UIButton buttonWithType:UIButtonTypeRoundedRect];
                [loginButton setFrame:CGRectMake(0.0f, 0.0f, 79.0f, 27.0f)];
                loginButton.titleLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                cell.accessoryView = loginButton;
                [loginButton setTitle:@"Выйти" forState:UIControlStateNormal];
                [loginButton addTarget:self action:@selector(loginButton:) forControlEvents:UIControlEventTouchDown];
                
                } 
                else cell.detailTextLabel.text = [[NSUserDefaults standardUserDefaults] objectForKey:@"login"];
                return cell;
            }
            
            case 1:{
                static NSString *CellIdentifier = @"Record";
                UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
                if (cell == nil) {
                //cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                
                cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
                    cell.backgroundColor = [UIColor whiteColor];
                cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                cell.textLabel.text=[curentEntrie objectAtIndex:indexPath.row];
                [cell addSubview:recordImage];
                }
                return cell;
            }
            case 2:{
                static NSString *CellIdentifier = @"Speed";
                UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
                if (cell == nil) {
                    cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
                    cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                    cell.backgroundColor = [UIColor whiteColor];
                cell.textLabel.text=[curentEntrie objectAtIndex:indexPath.row];
                cell.accessoryView = speedLabel;
                }
                return cell;
            }
            case 3:{
                static NSString *CellIdentifier = @"Switch";
                UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
                if (cell == nil) {
                    cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
                    cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                    cell.textLabel.text = [curentEntrie objectAtIndex:indexPath.row];
                cell.selectionStyle = UITableViewCellSelectionStyleNone;
                UISwitch *internetUploadSwitch = [[UISwitch alloc] initWithFrame:CGRectZero];
                cell.accessoryView = internetUploadSwitch;
                cell.backgroundColor = [UIColor whiteColor];
                [internetUploadSwitch setOn:[[NSUserDefaults standardUserDefaults] boolForKey:@"internetUserPreference"] animated:NO];
                [internetUploadSwitch addTarget:self action:@selector(internetUploadSwitch:) forControlEvents:UIControlEventValueChanged];
                }
                return cell;
            }
            case 4:{
                static NSString *CellIdentifier = @"LastTrip";
                UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
                if (cell == nil) {
                    cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
                    cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                    cell.backgroundColor = [UIColor whiteColor];
                    cell.textLabel.text = [curentEntrie objectAtIndex:indexPath.row];
                    cell.accessoryView = lastTrip;
                }
                return cell;
            }
            
            case 5: {
                static NSString *CellIdentifier = @"File";
                UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
                if (cell == nil) {
                    cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue1 reuseIdentifier:CellIdentifier];
                    cell.backgroundColor = [UIColor whiteColor];
                    cell.textLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                    cell.textLabel.text = [curentEntrie objectAtIndex:indexPath.row];
                    
                    
                    sendButton = [UIButton buttonWithType:UIButtonTypeRoundedRect];
                    [sendButton setFrame:CGRectMake(0.0f, 0.0f, 79.0f, 27.0f)];
                    sendButton.titleLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                    cell.accessoryView = sendButton;
                    [sendButton setTitle:@"Отправить" forState:UIControlStateNormal];
                    [sendButton addTarget:self action:@selector(sendButton:) forControlEvents:UIControlEventTouchDown];
                    
                } 
                return cell;
            }

            break;
        }
        
    }
    if (indexPath.section == 1) {

        switch( [indexPath row] ) {
            case 0: {
                static NSString *CellIdentifier = @"Segment";
                UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
                if (cell == nil) {
                    cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
                cell = [[UITableViewCell alloc] initWithFrame:CGRectMake(0, 0, 250, 35)];
                cell.backgroundColor = [UIColor whiteColor];
                UISegmentedControl *segmentedControl = [[UISegmentedControl alloc]  initWithItems: [NSArray arrayWithObjects: @"Посл. поездка", @"Все поездки", nil]];
                
                [segmentedControl setTitleTextAttributes:[NSDictionary dictionaryWithObject:[UIFont fontWithName:@"Trebuchet MS" size:14]
                                                                                     forKey:UITextAttributeFont] forState:UIControlStateNormal];
                segmentedControl.frame = CGRectMake(35, 5, 250, 35);//x,y,widht, height 
                segmentedControl.segmentedControlStyle = UISegmentedControlStylePlain;
                
                segmentedControl.selectedSegmentIndex = [[NSUserDefaults standardUserDefaults] integerForKey:@"segment"];
                
                [segmentedControl addTarget:self action:@selector(pickOne:) forControlEvents:UIControlEventValueChanged];
                
                
                
                [cell addSubview:segmentedControl];
                }
                return cell;
                
            }
            case 1:{
                static NSString *CellIdentifier = @"Quality";
                UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
                if (cell == nil) {
                    
                    cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
                    cell.backgroundColor = [UIColor whiteColor];
                    cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                    cell.textLabel.text = [curentEntrie objectAtIndex:indexPath.row];
                    cell.accessoryView = qualityDriving;
                }
                return cell;
            }
            case 2:{
                static NSString *CellIdentifier = @"Km";
                UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
                if (cell == nil) {
                    cell.backgroundColor = [UIColor whiteColor];
                    cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
                    cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                    cell.backgroundColor = [UIColor whiteColor];
                    cell.textLabel.text = [curentEntrie objectAtIndex:indexPath.row];
                    cell.accessoryView = countKm;
                }
                return cell;
            }
            case 3:{
                static NSString *CellIdentifier = @"SpeedMode";
                UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
                if (cell == nil) {
                    cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
                    cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                    cell.backgroundColor = [UIColor whiteColor];
                    cell.textLabel.text = [curentEntrie objectAtIndex:indexPath.row];
                    cell.accessoryView = speedMode;
                }
                return cell;
            }
            case 4:{
                
                static NSString *CellIdentifier = @"Acc";
                UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
                if (cell == nil) {
                    cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
                    cell.backgroundColor = [UIColor whiteColor];
                    cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                    cell.textLabel.text = [curentEntrie objectAtIndex:indexPath.row];
                cell.accessoryView = acceleration;
                }
                return cell;
            }
            case 5:{
                
                static NSString *CellIdentifier = @"Dec";
                UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
                if (cell == nil) {
                    cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
                    cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                    cell.backgroundColor = [UIColor whiteColor];
                    cell.textLabel.text = [curentEntrie objectAtIndex:indexPath.row];
                cell.accessoryView = deceleration;
                }
                return cell;
            }
            case 6:{
                static NSString *CellIdentifier = @"Rot";
                UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
                if (cell == nil) {
                    cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
                    cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                    cell.backgroundColor = [UIColor whiteColor];
                    cell.textLabel.text = [curentEntrie objectAtIndex:indexPath.row];
                cell.accessoryView = rotation;
                }
                return cell;
            }
            break;
        }
     }
     
    return nil;
}


- (void) pickOne:(id)sender{
    //проверка интернета
    NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
    
    UISegmentedControl *segmentedControl = (UISegmentedControl *)sender;
    if ([segmentedControl selectedSegmentIndex]==0) {
             NSLog(@"PickOne last");
            [userDefaults setInteger:0 forKey:@"segment"];
            [self parse: [userDefaults valueForKey:@"lastStat"] method:@"lastStat"];
        }
    else {
        NSLog(@"PickOne all");
        [userDefaults setInteger:1 forKey:@"segment"];
        [self parse: [userDefaults valueForKey:@"allStat"] method:@"allStat"];
    }
}
    

- (void)parse:(NSString *)result method:(NSString *)method{
    NSLog(@"result = %@", result);
    
    if (result != nil) {
        NSLog(@"parse result");
        SBJsonParser *jsonParser = [SBJsonParser new];
        NSArray *answer = [jsonParser objectWithString:result error:NULL];
        NSArray *statArray = [answer valueForKey:@"result"];
        qualityDriving.text = [NSString stringWithFormat:@"%@", [statArray valueForKey:@"total_score"]];
        speedMode.text = [NSString stringWithFormat:@"%@", [statArray valueForKey:@"score_speed"]];
        acceleration.text = [NSString stringWithFormat:@"%@", [statArray valueForKey:@"score_acc"]];
        deceleration.text = [NSString stringWithFormat:@"%@", [statArray valueForKey:@"score_brake"]];
        rotation.text = [NSString stringWithFormat:@"%@", [statArray valueForKey:@"score_turn"]];
        countKm.text = [NSString stringWithFormat:@"%@", [statArray valueForKey:@"distance"]];
        if ([method isEqualToString:@"lastStat"]) {
            NSDateFormatter * date_format = [[NSDateFormatter alloc] init];
            [date_format setLocale:[[NSLocale alloc] initWithLocaleIdentifier:@"ru_RU"]];
            //[date_format setTimeZone:[NSTimeZone timeZoneWithAbbreviation:@"GMT"]];
            [date_format setDateFormat: @"dd MMM HH:mm"];
           NSLog(@"date=%@", [NSDate dateWithTimeIntervalSince1970:[[statArray valueForKey:@"time"]doubleValue]]);
            NSString *string = [date_format stringFromDate:[NSDate dateWithTimeIntervalSince1970:[[statArray valueForKey:@"time"]doubleValue]]];
            lastTrip.text = string;
        }
        
    }
    else {
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Нет данных" message:@"Для вашей учетной записи еще нет данных" delegate:self cancelButtonTitle:@"ОК" otherButtonTitles:nil];
        [alert show];
        qualityDriving.text = @"?";
        speedMode.text = @"?";
        acceleration.text = @"?";
        deceleration.text = @"?";
        rotation.text = @"?";
        countKm.text = @"?";
        lastTrip.text = @"?";
    }

}

- (IBAction) internetUploadSwitch:(id)sender{
    if ([sender isOn])
    {
        //only wi-fi
        NSLog(@"switch - only wi-fi");
        [[NSUserDefaults standardUserDefaults] setBool:YES forKey:@"internetUserPreference"];
    }
    else
    {
        NSLog(@"switch - internet - 3G");
        [[NSUserDefaults standardUserDefaults] setBool:NO forKey:@"internetUserPreference"]; 
    }
}

/*
// Override to support conditional editing of the table view.
- (BOOL)tableView:(UITableView *)tableView canEditRowAtIndexPath:(NSIndexPath *)indexPath
{
    // Return NO if you do not want the specified item to be editable.
    return YES;
}
*/

/*
// Override to support editing the table view.
- (void)tableView:(UITableView *)tableView commitEditingStyle:(UITableViewCellEditingStyle)editingStyle forRowAtIndexPath:(NSIndexPath *)indexPath
{
    if (editingStyle == UITableViewCellEditingStyleDelete) {
        // Delete the row from the data source
        [tableView deleteRowsAtIndexPaths:[NSArray arrayWithObject:indexPath] withRowAnimation:UITableViewRowAnimationFade];
    }   
    else if (editingStyle == UITableViewCellEditingStyleInsert) {
        // Create a new instance of the appropriate class, insert it into the array, and add a new row to the table view
    }   
}
*/

/*
// Override to support rearranging the table view.
- (void)tableView:(UITableView *)tableView moveRowAtIndexPath:(NSIndexPath *)fromIndexPath toIndexPath:(NSIndexPath *)toIndexPath
{
}
*/

/*
// Override to support conditional rearranging of the table view.
- (BOOL)tableView:(UITableView *)tableView canMoveRowAtIndexPath:(NSIndexPath *)indexPath
{
    // Return NO if you do not want the item to be re-orderable.
    return YES;
}
*/
- (IBAction)loginButton:(id)sender{
    NSLog(@"logout");
    NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
    [userDefaults removeObjectForKey:@"login"];
    [userDefaults removeObjectForKey:@"password"];
    [userDefaults removeObjectForKey:@"cookie"];
    [userDefaults removeObjectForKey:@"allStat"];
    [userDefaults removeObjectForKey:@"lastStat"];
    [[NSUserDefaults standardUserDefaults] setBool:NO forKey:@"internetUserPreference"]; 
    [userDefaults synchronize];
    
    AuthViewController *authView = [self.storyboard instantiateViewControllerWithIdentifier: @"AuthViewController"];
    authView.modalTransitionStyle = UIModalTransitionStyleFlipHorizontal;
    [self presentModalViewController: authView animated:YES];
}

- (IBAction)sendButton:(id)sender{
    
    
    MFMailComposeViewController *picker = [[MFMailComposeViewController alloc] initWithNibName:@"Email" bundle:nil];
    picker.mailComposeDelegate = self;
    
	// Set the subject of email
    [picker setSubject:@"NTI"];
    
	// Add email addresses
    // Notice three sections: "to" "cc" and "bcc"	
    [picker setToRecipients:[NSArray arrayWithObjects: @"peacock7team@gmail.com", nil]];		
    
	// Fill out the email body text
	NSString *emailBody = @"NTI log file";
    
	// This is not an HTML formatted email
	[picker setMessageBody:emailBody isHTML:NO];
    
    
    fileController = [[FileController alloc] init];
    NSData *attachment = [fileController makeArchive];
    
    // Attach  data to the email
    
	
	[picker addAttachmentData:attachment mimeType:@"application/zip" fileName:@"LOG"];
    
    
	// Show email view
	
	[self presentModalViewController:picker animated:YES];
    
	// Release picker
    //[self dismissModalViewControllerAnimated:YES];
    
}

- (void)mailComposeController:(MFMailComposeViewController*)controller didFinishWithResult:(MFMailComposeResult)result error:(NSError*)error 
{	
	[self dismissModalViewControllerAnimated:YES];
    if (error == nil) {
        [fileController deleteFile];
    }
}
 

- (IBAction)helpButton:(id)sender{
    NSLog(@"Help Button Pushed");
    StatHelpViewController *statHelpView = [self.storyboard instantiateViewControllerWithIdentifier: @"StatHelpViewController"];
    statHelpView.modalTransitionStyle = UIModalTransitionStylePartialCurl;
    [self presentModalViewController: statHelpView animated:YES];
    
}



#pragma mark - Table view delegate

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    // Navigation logic may go here. Create and push another view controller.
    /*
     <#DetailViewController#> *detailViewController = [[<#DetailViewController#> alloc] initWithNibName:@"<#Nib name#>" bundle:nil];
     // ...
     // Pass the selected object to the new view controller.
     [self.navigationController pushViewController:detailViewController animated:YES];
     */
}
//- (CGFloat) tableView:(UITableView *)tableView heightForHeaderInSection:(NSInteger)section
//{
//    return 100.0;
//}




@end
