//
//  Email.h
//  NTI
//
//  Created by Елена on 11.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <MessageUI/MessageUI.h>
#import <MessageUI/MFMailComposeViewController.h>
#import "FileController.h"

@interface Email : UIViewController <UINavigationControllerDelegate, MFMailComposeViewControllerDelegate> {
    FileController *fileController;
    UIViewController *firstView;
    
}

- (void)sendFile;
- (id)initWith: (UIViewController *)viewController;

@end
