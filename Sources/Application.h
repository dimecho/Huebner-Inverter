#import <Cocoa/Cocoa.h>
#import <WebKit/WebKit.h>
#import "Terminal.h"

@interface Application : NSObject {
    IBOutlet WebView* webView;
    IBOutlet NSWindow *window;
    
    //Serial ports
    NSString* stream[32] ;
    NSString* path[32] ;
    NSString* tty;
}

@property (nonatomic, strong) NSTask *phpTask;
@property (nonatomic, strong) Terminal *terminal;

@end
