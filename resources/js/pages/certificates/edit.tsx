import AppLayout from '@/layouts/app-layout';
import { index, update } from '@/routes/certificates';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { ArrowLeft, Upload, FileText } from 'lucide-react';
import { useState } from 'react';

interface Rul {
    id: number;
    uuid: string;
    name: string;
    contact_number: string;
}

interface Certificate {
    uuid: string;
    rul_id: number;
    certificate_name: string;
    file_path: string;
    rul: Rul;
}

interface EditProps {
    certificate: Certificate;
    ruls: Rul[];
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Certificates',
        href: index().url,
    },
    {
        title: 'Edit',
        href: '#',
    },
];

export default function Edit({ certificate, ruls }: EditProps) {
    const { data, setData, post, processing, errors } = useForm({
        rul_id: certificate.rul_id.toString(),
        certificate_name: certificate.certificate_name,
        certificate_file: null as File | null,
        _method: 'PUT',
    });

    const [fileName, setFileName] = useState<string>('');

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            setData('certificate_file', file);
            setFileName(file.name);
        }
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(update({ certificate: certificate.uuid }).url, {
            forceFormData: true,
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Edit Certificate" />
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
                                <CardTitle>Edit Certificate</CardTitle>
                                <CardDescription>
                                    Update certificate information
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-6">
                            <div className="space-y-4">
                                <div className="space-y-2">
                                    <Label htmlFor="rul_id">
                                        Resource Unit Leader <span className="text-red-500">*</span>
                                    </Label>
                                    <Select
                                        value={data.rul_id}
                                        onValueChange={(value) => setData('rul_id', value)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select a RUL" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {ruls.map((rul) => (
                                                <SelectItem key={rul.id} value={rul.id.toString()}>
                                                    {rul.name} - {rul.contact_number}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.rul_id && (
                                        <p className="text-sm text-red-500">{errors.rul_id}</p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="certificate_name">
                                        Certificate Name <span className="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="certificate_name"
                                        value={data.certificate_name}
                                        onChange={(e) =>
                                            setData('certificate_name', e.target.value)
                                        }
                                        placeholder="Enter certificate name"
                                    />
                                    {errors.certificate_name && (
                                        <p className="text-sm text-red-500">
                                            {errors.certificate_name}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label>Current File</Label>
                                    <div className="flex items-center gap-2 p-3 border rounded-md bg-gray-50">
                                        <FileText className="h-4 w-4 text-gray-600" />
                                        <span className="text-sm text-gray-600">
                                            {certificate.file_path.split('/').pop()}
                                        </span>
                                        <a
                                            href={`/storage/${certificate.file_path}`}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="ml-auto text-sm text-blue-600 hover:underline"
                                        >
                                            View
                                        </a>
                                    </div>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="certificate_file">
                                        Replace File (Optional)
                                    </Label>
                                    <div className="flex items-center gap-4">
                                        <Input
                                            id="certificate_file"
                                            type="file"
                                            onChange={handleFileChange}
                                            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                            className="hidden"
                                        />
                                        <label htmlFor="certificate_file">
                                            <Button
                                                type="button"
                                                variant="outline"
                                                className="cursor-pointer"
                                                asChild
                                            >
                                                <span>
                                                    <Upload className="mr-2 h-4 w-4" />
                                                    Choose New File
                                                </span>
                                            </Button>
                                        </label>
                                        {fileName && (
                                            <span className="text-sm text-gray-600">
                                                {fileName}
                                            </span>
                                        )}
                                    </div>
                                    <p className="text-xs text-gray-500">
                                        Accepted formats: JPG, PNG, PDF, DOC, DOCX (Max: 10MB)
                                    </p>
                                    {errors.certificate_file && (
                                        <p className="text-sm text-red-500">
                                            {errors.certificate_file}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="flex gap-4">
                                <Button type="submit" disabled={processing}>
                                    {processing ? 'Updating...' : 'Update Certificate'}
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
