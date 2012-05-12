//
//  StatHelpViewController.m
//  NTI
//
//  Created by Елена on 29.04.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "StatHelpViewController.h"

@interface StatHelpViewController ()

@end

@implementation StatHelpViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad
{
    scrollView.contentSize = CGSizeMake(320, 1250);
    [super viewDidLoad];
	// Do any additional setup after loading the view.
}

- (IBAction)closeHelp:(id)sender{
    
    [self dismissModalViewControllerAnimated:YES];
}

- (void)viewDidUnload
{
    [super viewDidUnload];
    // Release any retained subviews of the main view.
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

@end
