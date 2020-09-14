import Rubric from "./Domain/Rubric";

export const logClass:ClassDecorator = (constructor: Function) => {
    console.log(constructor.name + ' created');
};

export const logMethod:MethodDecorator = ((target, propertyKey, descriptor:TypedPropertyDescriptor<any>) => {
    let originalMethod = descriptor.value;
    descriptor.value = function (...args: any[]) {
        console.log("Called " + propertyKey.toString() + " on " + this + " with params: " + args.map(arg => arg.toString()). join(', '));
        let returned = originalMethod.apply(this, args);
        console.log(returned);
        return returned;
    }
});
