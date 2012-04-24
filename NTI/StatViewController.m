//
//  StatViewController.m
//  NTI
//
//  Created by Mike on 07.03.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "StatViewController.h"

#define ROWSNUMBER 10

@implementation StatViewController
@synthesize writeAction;
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
    [super viewDidLoad];
    UIFont *fontForLabel = [UIFont fontWithName:@"Trebuchet MS" size:16]; 
    
    //инициализация лейблов для таблицы
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
    
    
    recordImage = [[UIImageView alloc] initWithFrame:CGRectMake(280.0f, 7.0f, 27.0f, 27.0f)];
    
    if ([myAppDelegate canWriteToFile]) {
        [recordImage setImage:[UIImage imageNamed:@"green.png"]];
        //[sendButton setHidden:YES];
        //[sendButton setUserInteractionEnabled:NO];
        //sendButton.userInteractionEnabled = NO;
    }
    else {
        [recordImage setImage:[UIImage imageNamed:@"red.png"]];
        //[sendButton setEnabled: YES];
    }
    
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(changeImage)
     name: @"canWriteToFile"
     object: nil];
}


- (void)changeImage{
    
    if ([myAppDelegate canWriteToFile]) {
        [recordImage setImage:[UIImage imageNamed:@"green.png"]];
        [sendButton setUserInteractionEnabled:NO];
    }
    else {
        [recordImage setImage:[UIImage imageNamed:@"red.png"]];
    }
    
}

- (void)viewDidUnload
{
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    
    //Прослушка notifications
    [[NSNotificationCenter defaultCenter]	
     addObserver: self
     selector: @selector(speedUpdate)
     name: @"locateNotification"
     object: nil]; 

}

- (void)viewDidDisappear:(BOOL)animated
{
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

#pragma mark - Table view data source

/*
- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
#warning Potentially incomplete method implementation.
    // Return the number of sections.
    return 1;
}
*/
- (NSString *)tableView:(UITableView *)tableView titleForHeaderInSection:(NSInteger)section

{
    return @"Ваша статистика";
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return ROWSNUMBER;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    NSString *name = [[NSUserDefaults standardUserDefaults] objectForKey:@"login"];
             
    switch( [indexPath row] ) {
            
        case 0: {
            static NSString *CellIdentifier = @"change";
            
            UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
            
            if (cell == nil) {
                //cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue2 reuseIdentifier:CellIdentifier];
                //cell.textLabel.text=@" ";
                cell = [[UITableViewCell alloc] initWithFrame:CGRectMake(50, 50, 250, 35)];
            
            UISegmentedControl *segmentedControl = [[UISegmentedControl alloc]  initWithItems: [NSArray arrayWithObjects: @"Last", @"All", nil]];
            segmentedControl.frame = CGRectMake(35, 5, 250, 35);//x,y,widht, height 
            segmentedControl.segmentedControlStyle = UISegmentedControlStylePlain;
            
            segmentedControl.selectedSegmentIndex = 0;
             
            [segmentedControl addTarget:self action:@selector(pickOne:) forControlEvents:UIControlEventValueChanged];
        
            [cell addSubview:segmentedControl];
           // cell.accessoryView = segmentedControl;
                //cell.textLabel.textAlignment = UITextAlignmentCenter;
            
            }
            return cell; 
             
        }
        case 1: {
            
            static NSString *CellIdentifier = @"Name";
            
            UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
            if (cell == nil) {
                cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue1 reuseIdentifier:CellIdentifier];
                cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                cell.textLabel.text=@"Имя";
                if (name == nil) {
                    cell.detailTextLabel.text = @"";
                }
                else cell.detailTextLabel.text = name;
                
                loginButton = [UIButton buttonWithType:UIButtonTypeRoundedRect];
                [loginButton setFrame:CGRectMake(0.0f, 0.0f, 79.0f, 27.0f)];
                loginButton.titleLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:15];
                cell.accessoryView = loginButton;
                [loginButton setTitle:@"Выйти" forState:UIControlStateNormal];
                [loginButton addTarget:self action:@selector(loginButton:) forControlEvents:UIControlEventTouchDown];
                
                
            }
            return cell;
        }
            
        case 2: {
            static NSString *CellIdentifier = @"Record";
            
            UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
            if (cell == nil) {
                


                cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
                cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                cell.textLabel.text=@"Запись";
                [cell addSubview:recordImage];
                
                
                                
            }
            return cell;
        }

        case 3: {
            static NSString *CellIdentifier = @"Speed";
            
            UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
            if (cell == nil) {
                cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
                cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                cell.textLabel.text=@"Скорость";
                cell.accessoryView = speedLabel;
            }
            return cell;
        }
        case 4:{
            static NSString *CellIdentifier = @"1";
            
            UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
            if (cell == nil) {
                cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
                cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                cell.textLabel.text=@"Общая оценка";
                cell.accessoryView = qualityDriving;
            }
            return cell;
        }
        
        case 5:{
            static NSString *CellIdentifier = @"2";
            
            UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
            if (cell == nil) {
                cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
                cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                cell.textLabel.text=@"Соблюдение скор. режима";
                cell.accessoryView = speedMode;
            }
            return cell;
        }
            
        case 6:{
            static NSString *CellIdentifier = @"3";
            
            UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
            if (cell == nil) {
                cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
                cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                cell.textLabel.text=@"Качество разгонов";
                cell.accessoryView = acceleration;
            }
            return cell;
        }
        case 7:{
            static NSString *CellIdentifier = @"4";
            
            UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
            if (cell == nil) {
                cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
                cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                cell.textLabel.text=@"Качество торможения";
                cell.accessoryView = deceleration;
            }
            return cell;
        }
        case 8:{
            static NSString *CellIdentifier = @"5";
            
            UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
            if (cell == nil) {
                cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
                cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                cell.textLabel.text=@"Качество поворотов";
                cell.accessoryView = rotation;
            }
            return cell;
        }
            
        case 9:{
            static NSString *CellIdentifier = @"6";
            
            UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
            if (cell == nil) {
       
                cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier];
                cell.textLabel.font = cell.detailTextLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:16];
                cell.textLabel.text=@"Файл";
                                
                sendButton = [UIButton buttonWithType:UIButtonTypeRoundedRect];
                [sendButton setFrame:CGRectMake(0.0f, 0.0f, 79.0f, 27.0f)];
                sendButton.titleLabel.font = [UIFont fontWithName:@"Trebuchet MS" size:15];
                cell.accessoryView = sendButton;
                [sendButton setTitle:@"Отправить" forState:UIControlStateNormal];
                [sendButton addTarget:self action:@selector(sendButton:) forControlEvents:UIControlEventTouchDown];

            }
            return cell;
        }
       

        

        
        break;
    }
    return nil;
}


- (void) pickOne:(id)sender{
    //проверка интернета
    ServerCommunication *serverCommunication = [[ServerCommunication alloc] init];
    
        UISegmentedControl *segmentedControl = (UISegmentedControl *)sender;
        if ([segmentedControl selectedSegmentIndex]==0) {
            NSLog(@"за последнюю поездку");
        
            NSString *result=[serverCommunication getLastStatistic];
            if (![result isEqualToString:@"error"]) {
                [[NSUserDefaults standardUserDefaults] setValue:result forKey:@"lastStat"];
                [self parse: result];
            }
            else {
                UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Ошибка!" message:@"Повторите попытку позже" delegate:self cancelButtonTitle:@"ОК" otherButtonTitles:nil];
                [alert show];
            }
            
        }
    else {
        NSLog(@"за все время");
        NSString *result=[serverCommunication getAllStatistic];
        if (![result isEqualToString:@"error"]) {
            [[NSUserDefaults standardUserDefaults] setValue:result forKey:@"allStat"];
            [self parse: result];
        }
        else {
            UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Ошибка!" message:@"Повторите попытку позже" delegate:self cancelButtonTitle:@"ОК" otherButtonTitles:nil];
            [alert show];
        }

        
    }
    //[segmentedControl titleForSegmentAtIndex: [segmentedControl selectedSegmentIndex]];
}

- (void)parse:(NSString *)result{
    NSLog(@"result = %@", result);
    SBJsonParser *jsonParser = [SBJsonParser new];
    NSArray *answer = [jsonParser objectWithString:result error:NULL];
    NSArray *statArray = [answer valueForKey:@"result"];
    qualityDriving.text = [NSString stringWithFormat:@"%@", [statArray valueForKey:@"total_score"]];
    speedMode.text = [NSString stringWithFormat:@"%@", [statArray valueForKey:@"score_speed"]];
    acceleration.text = [NSString stringWithFormat:@"%@", [statArray valueForKey:@"score_acc"]];
    deceleration.text = [NSString stringWithFormat:@"%@", [statArray valueForKey:@"score_brake"]];
    rotation.text = [NSString stringWithFormat:@"%@", [statArray valueForKey:@"score_turn"]];
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
    NSUserDefaults *userDefaults = [NSUserDefaults standardUserDefaults];
    [userDefaults removeObjectForKey:@"login"];
    [userDefaults removeObjectForKey:@"password"];
    [userDefaults removeObjectForKey:@"cookie"];
    [userDefaults synchronize];
    
    AuthViewController *authView = [self.storyboard instantiateViewControllerWithIdentifier: @"AuthViewController"];
    authView.modalTransitionStyle = UIModalTransitionStyleFlipHorizontal;
    [self presentModalViewController: authView animated:YES];
}

- (IBAction)sendButton:(id)sender{
    
    
    ServerCommunication *serverCommunication = [[ServerCommunication alloc] init];
    
    if ([serverCommunication checkInternetConnection]){
        [serverCommunication refreshCookie];
        [[myAppDelegate recordAction] sendFile];
    }
    else {
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Ошибка!" message:@"Отсутствует Интернет-соединение. Включите Интернет и повторите попытку" delegate:self cancelButtonTitle:@"ОК" otherButtonTitles:nil];
        [alert show];
    }
    
    
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

@end
