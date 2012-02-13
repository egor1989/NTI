//
//  SecondViewController.h
//  NTI
//
//  Created by Елена on 05.02.12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface SecondViewController : UIViewController{
}

- (void)refreshPlot:(NSNotification*)theNotice;
@property (getter = getX) double x;
@property (getter = getY) double y;
@end
