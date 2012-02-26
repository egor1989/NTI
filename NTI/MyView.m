//
//  MyView.m
//  window
//
//  Created by Kitsune on 13/05/09.
//  Copyright 2009 __MyCompanyName__. All rights reserved.
//

#import "MyView.h"
#define centrX 170.0
#define centrY 215.0

@implementation MyView

- (void) refreshAccLine:(NSNotification*)theNotice{
    x=[((NSNumber*)[theNotice.userInfo objectForKey: @"accX"]) doubleValue];
    y=[((NSNumber*)[theNotice.userInfo objectForKey: @"accY"]) doubleValue];
    [self setNeedsDisplay];
}

/*
- (void) refreshGPSLine:(NSNotification*)theNotice{
    course = [myAppDelegate lastLoc].course;
    speed = [myAppDelegate lastLoc].speed;
    course=course/180*M_PI;
    [self setNeedsDisplay];
}
 */

-(void) method{
    self.clearsContextBeforeDrawing=YES;
    [[NSNotificationCenter defaultCenter]
     addObserver: self
     selector: @selector(refreshAccLine:)
     name: @"plotNotification"
     object: nil];
    
//    [[NSNotificationCenter defaultCenter]	
//     addObserver: self
//     selector: @selector(refreshGPSLine:)
//     name: @"locateNotification"
//     object: nil];
}

-(id) initWithCoder:(NSCoder *)aDecoder{
    self=[super initWithCoder:aDecoder];
    [self method];
    return self;
}


- (void)drawRect:(CGRect)rect {
	CGContextRef c = UIGraphicsGetCurrentContext();
	CGContextSetLineWidth (c,5);
    
    [[UIColor redColor] set];
    CGContextMoveToPoint(c, centrX, centrY);
	CGContextAddLineToPoint(c, centrX+x*150.0, centrY-y*150.0);
	CGContextStrokePath(c);
    
//    [[UIColor blueColor] set];
//    CGContextMoveToPoint(c, centrX, centrY);
//	CGContextAddLineToPoint(c, centrX+speed*sin(M_PI - course), centrY+speed*cos(M_PI -course));
//	CGContextStrokePath(c);
    
    [[UIColor blackColor] set];
    CGContextFillEllipseInRect(c, CGRectMake(centrX-3, centrY-2, 7, 7));
	CGContextStrokePath(c);
}

@end
