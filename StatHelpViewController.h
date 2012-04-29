//
//  StatHelpViewController.h
//  NTI
//
//  Created by Елена on 29.04.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface StatHelpViewController : UIViewController{
    IBOutlet UIScrollView *scrollView;
    IBOutlet UIButton *closeHelp;
}

- (IBAction)closeHelp:(id)sender;

@end
