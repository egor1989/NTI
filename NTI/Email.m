//
//  Email.m
//  NTI
//
//  Created by Елена on 11.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "Email.h"


@implementation Email

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        fileController = [[FileController alloc] init];
        
    }
    return self;
}

- (id)initWith: (UIViewController *)viewController{
    self = [super init];
    firstView = viewController;
    return self;
}

- (void)didReceiveMemoryWarning
{
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

- (void)sendFile
{
    MFMailComposeViewController *picker = [[MFMailComposeViewController alloc] initWithNibName:@"Email" bundle:nil];
    picker.mailComposeDelegate = self;
    
    
    
	// Set the subject of email
    [picker setSubject:@"NTI!"];
    
	// Add email addresses
    // Notice three sections: "to" "cc" and "bcc"	
	[picker setToRecipients:[NSArray arrayWithObjects:@"alekseenko.lena@gmail.com", nil]];
	//[picker setCcRecipients:[NSArray arrayWithObject:@"emailaddress3@domainName.com"]];	
	    
	// Fill out the email body text
	NSString *emailBody = @"JSON data.";
    
	// This is not an HTML formatted email
	[picker setMessageBody:emailBody isHTML:NO];
   
    
    NSInteger count = [fileController countFiles]; 
    NSData *attachment = [[fileController getAttachment:5] dataUsingEncoding:NSUTF8StringEncoding];
    //NSLog(@"%@",[fileController getAttachment:5]);
    NSArray *filesArray = [fileController arrayFiles];
    NSString *name = [filesArray objectAtIndex:5];
    NSLog(@"%@",name);
    
    // Attach  data to the email
	//NSArray *attachments = nil; 
	[picker addAttachmentData:attachment mimeType:@"text/plain" fileName:name];
    
    attachment = [[fileController getAttachment:6] dataUsingEncoding:NSUTF8StringEncoding];
    //NSLog(@"%@",[fileController getAttachment:6]);
    //filesArray = [fileController arrayFiles];
    name = [filesArray objectAtIndex:6];
    [picker addAttachmentData:attachment mimeType:@"text/plain" fileName:name];
    

	 
	// Show email view
	
	[firstView presentModalViewController:picker animated:YES];
    
	// Release picker

}

- (void)mailComposeController:(MFMailComposeViewController*)controller didFinishWithResult:(MFMailComposeResult)result error:(NSError*)error 
{	
	[firstView dismissModalViewControllerAnimated:YES];
}





#pragma mark - View lifecycle

/*
// Implement loadView to create a view hierarchy programmatically, without using a nib.
- (void)loadView
{
}
*/

/*
// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad
{
    [super viewDidLoad];
}
*/

- (void)viewDidUnload
{
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    // Return YES for supported orientations
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

@end
