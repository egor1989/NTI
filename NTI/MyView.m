//
//  MyView.m
//  window
//
//  Created by Kitsune on 13/05/09.
//  Copyright 2009 __MyCompanyName__. All rights reserved.
//

#import "MyView.h"


@implementation MyView

- (void) refreshPlot:(NSNotification*)theNotice{
    x=[((NSNumber*)[theNotice.userInfo objectForKey: @"accX"]) doubleValue];
    y=[((NSNumber*)[theNotice.userInfo objectForKey: @"accY"]) doubleValue];
}

- (id)initWithFrame:(CGRect)frame {
    if (self = [super initWithFrame:frame]) {
    }
    return self;
}

-(void) method{
//    if(f){
    self.clearsContextBeforeDrawing=YES;
        [[NSNotificationCenter defaultCenter]
         addObserver: self
         selector: @selector(refreshPlot:)
         name: @"plotNotification"
         object: nil];
        f=false;
//    }
}

- (void)drawRect:(CGRect)rect {
    [self method ];
	CGContextRef c = UIGraphicsGetCurrentContext();
	
	CGFloat red[4] = {1.0f, 0.0f, 0.0f, 1.0f};
	CGContextSetStrokeColor(c, red);
	CGContextBeginPath(c);
//	CGContextMoveToPoint(c, 320.0f, 470.0f);
    CGContextMoveToPoint(c, 170.0f, 235.0f);
	CGContextAddLineToPoint(c, x*150.0+170.0, y*150.0+235.0);
    NSLog(@"%f",x);
	CGContextStrokePath(c);
}



@end
