import AppLayout from '@/layouts/app-layout';
import { index, update } from '@/routes/personnels';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { ArrowLeft } from 'lucide-react';

interface Personnel {
    uuid: string;
    name: string;
    contact_number: string;
    serial_number: string;
    department: string;
}

interface EditProps {
    personnel: Personnel;
}

const breadcrumbs = (personnelUuid: string): BreadcrumbItem[] => [
    {
        title: 'Personnels',
        href: index().url,
    },
    {
        title: 'Edit',
        href: '#',
    },
];

export default function Edit({ personnel }: EditProps) {
    const { data, setData, put, processing, errors } = useForm({
        name: personnel.name,
        contact_number: personnel.contact_number,
        serial_number: personnel.serial_number,
        department: personnel.department,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(update({ personnel: personnel.uuid }).url);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs(personnel.uuid)}>
            <Head title="Edit Personnel" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <Card>
                    <CardHeader>
                        <div className="flex items-center gap-4">
                            <Link href={index().url}>
                                <Button variant="outline" size="icon">
                                    <ArrowLeft className="h-4 w-4" />
                                </Button>
                            </Link>
                            <div>
                                <CardTitle>Edit Personnel</CardTitle>
                                <CardDescription>
                                    Update personnel information
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-6">
                            <div className="grid gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="name">
                                        Name <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="name"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        placeholder="Enter name"
                                    />
                                    {errors.name && (
                                        <p className="text-sm text-red-500">{errors.name}</p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="serial_number">
                                        Serial Number <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="serial_number"
                                        value={data.serial_number}
                                        onChange={(e) =>
                                            setData('serial_number', e.target.value)
                                        }
                                        placeholder="Enter serial number"
                                    />
                                    {errors.serial_number && (
                                        <p className="text-sm text-red-500">
                                            {errors.serial_number}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="contact_number">
                                        Contact Number <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="contact_number"
                                        value={data.contact_number}
                                        onChange={(e) =>
                                            setData('contact_number', e.target.value)
                                        }
                                        placeholder="Enter contact number"
                                    />
                                    {errors.contact_number && (
                                        <p className="text-sm text-red-500">
                                            {errors.contact_number}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="department">
                                        Department <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="department"
                                        value={data.department}
                                        onChange={(e) => setData('department', e.target.value)}
                                        placeholder="Enter department"
                                    />
                                    {errors.department && (
                                        <p className="text-sm text-red-500">
                                            {errors.department}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="flex gap-4">
                                <Button type="submit" disabled={processing}>
                                    {processing ? 'Updating...' : 'Update Personnel'}
                                </Button>
                                <Link href={index().url}>
                                    <Button type="button" variant="outline">
                                        Cancel
                                    </Button>
                                </Link>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
